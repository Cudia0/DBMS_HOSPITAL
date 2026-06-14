<?php

namespace backend\controllers;

use common\models\TblAppointment;
use common\models\AppointmentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use Yii;

/**
 * AppointmentController - Receptionist & Director manage, Doctor views own
 */
class AppointmentController extends Controller
{
    public function actionGetDetails($id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $appointment = TblAppointment::findOne($id);
        if ($appointment) {
            return [
                'success' => true,
                'patient_id' => $appointment->patient_id,
                'dr_id' => $appointment->dr_id,
                'status' => $appointment->status,
                'appointment_date' => $appointment->appointment_date,
                'appointment_time' => $appointment->appointment_time
            ];
        }
        return ['success' => false];
    }

    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        [
                            'allow' => true,
                            'actions' => ['index', 'view'],
                            'roles' => ['@'],
                            'matchCallback' => function ($rule, $action) {
                                $user = Yii::$app->user->identity;
                                return $user->isDirector() || $user->isReceptionist() || $user->isDoctor();
                            },
                        ],
                        [
                            'allow' => true,
                            'actions' => ['create', 'update', 'accept', 'reject', 'check-in', 'delete'],
                            'roles' => ['@'],
                            'matchCallback' => function ($rule, $action) {
                                $user = Yii::$app->user->identity;
                                if ($user->isDirector()) {
                                    return true;
                                }
                                if ($user->isReceptionist() && in_array($action->id, ['create', 'update', 'accept', 'reject', 'check-in'])) {
                                    return true;
                                }
                                return false;
                            },
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                        'accept' => ['POST'],
                        'reject' => ['POST'],
                        'check-in' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists appointments - Filtered for doctor to show only their own
     */
    public function actionIndex()
    {
        $searchModel = new AppointmentSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        
        $user = Yii::$app->user->identity;
        
        // If doctor, only show their appointments
        if ($user->isDoctor()) {
            $dataProvider->query->andWhere(['dr_id' => $user->doctor_id]);
        }
        
        $dataProvider->query->orderBy([
            'status' => SORT_ASC,
            'appointment_date' => SORT_DESC, 
            'appointment_time' => SORT_DESC
        ]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single appointment - Doctor can only view their own
     */
    public function actionView($appt_id)
    {
        $model = $this->findModel($appt_id);
        
        // If doctor, ensure they can only view their own appointments
        $user = Yii::$app->user->identity;
        if ($user->isDoctor() && $model->dr_id !== $user->doctor_id) {
            throw new \yii\web\ForbiddenHttpException('You can only view your own appointments.');
        }
        
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    public function actionCreate()
    {
        $model = new TblAppointment();
        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                $user = Yii::$app->user->identity;
                if ($user->isReceptionist() && !$model->recep_id) {
                    $model->recep_id = $user->receptionist_id;
                }
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Appointment created successfully.');
                    return $this->redirect(['view', 'appt_id' => $model->appt_id]);
                }
            }
        } else {
            $model->loadDefaultValues();
        }
        return $this->render('create', ['model' => $model]);
    }

    public function actionAccept($appt_id)
    {
        $model = $this->findModel($appt_id);
        $user = Yii::$app->user->identity;
        
        if (!$user->isDirector() && !$user->isReceptionist()) {
            Yii::$app->session->setFlash('error', 'You do not have permission to accept appointments.');
            return $this->redirect(['view', 'appt_id' => $model->appt_id]);
        }
        
        if ($model->status === null || $model->status === '') {
            if (Yii::$app->request->isPost) {
                $model->load(Yii::$app->request->post());
                
                if (empty($model->appointment_date) || empty($model->appointment_time)) {
                    Yii::$app->session->setFlash('error', 'Please set the appointment date and time before accepting.');
                    return $this->redirect(['view', 'appt_id' => $model->appt_id]);
                }
                
                $model->status = 'scheduled';
                if ($user->isReceptionist()) {
                    $model->recep_id = $user->receptionist_id;
                }
                
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', '✅ Appointment accepted and scheduled.');
                }
            }
        } else {
            Yii::$app->session->setFlash('warning', 'This appointment is already processed.');
        }
        
        return $this->redirect(['view', 'appt_id' => $model->appt_id]);
    }

    public function actionReject($appt_id)
    {
        $model = $this->findModel($appt_id);
        $user = Yii::$app->user->identity;
        
        if (!$user->isDirector() && !$user->isReceptionist()) {
            Yii::$app->session->setFlash('error', 'You do not have permission to reject appointments.');
            return $this->redirect(['view', 'appt_id' => $model->appt_id]);
        }
        
        if ($model->status === null || $model->status === '' || $model->status === 'scheduled') {
            if (Yii::$app->request->isPost) {
                $rejectReason = Yii::$app->request->post('reject_reason', '');
                $model->status = 'cancelled';
                if ($user->isReceptionist()) {
                    $model->recep_id = $user->receptionist_id;
                }
                if ($rejectReason) {
                    $model->symptoms_list = $model->symptoms_list . "\n\n[CANCELLED]\nReason: " . $rejectReason;
                }
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', '❌ Appointment request has been cancelled.');
                }
            }
        } else {
            Yii::$app->session->setFlash('warning', 'This appointment cannot be cancelled. Current status: ' . $model->status);
        }
        
        return $this->redirect(['view', 'appt_id' => $model->appt_id]);
    }

    public function actionCheckIn($appt_id)
    {
        $model = $this->findModel($appt_id);
        $user = Yii::$app->user->identity;
        
        if (!$user->isDirector() && !$user->isReceptionist()) {
            Yii::$app->session->setFlash('error', 'You do not have permission to check in patients.');
            return $this->redirect(['view', 'appt_id' => $model->appt_id]);
        }
        
        if ($model->status === 'scheduled') {
            $model->status = 'checked_in';
            if ($model->save()) {
                Yii::$app->session->setFlash('success', '✅ Patient checked in successfully.');
            }
        } else {
            Yii::$app->session->setFlash('warning', 'Appointment must be in scheduled status to check in.');
        }
        
        return $this->redirect(['view', 'appt_id' => $model->appt_id]);
    }

    public function actionUpdate($appt_id)
    {
        $model = $this->findModel($appt_id);
        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Appointment updated successfully.');
            return $this->redirect(['view', 'appt_id' => $model->appt_id]);
        }
        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete($appt_id)
    {
        if (!Yii::$app->user->identity->isDirector()) {
            Yii::$app->session->setFlash('error', 'Only Director can delete appointments.');
            return $this->redirect(['index']);
        }
        $this->findModel($appt_id)->delete();
        return $this->redirect(['index']);
    }

    protected function findModel($appt_id)
    {
        if (($model = TblAppointment::findOne(['appt_id' => $appt_id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}