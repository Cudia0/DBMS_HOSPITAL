<?php

namespace frontend\controllers;

use common\models\TblAppointment;
use common\models\TblDoctor;
use common\models\TblPatient;
use common\models\AppointmentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use Yii;

/**
 * AppointmentController - Patient's appointment management
 * Patients can: index (view own), view, create (book)
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
                            'actions' => ['index', 'view', 'create'],
                            'roles' => ['@'],
                            'matchCallback' => function ($rule, $action) {
                                return Yii::$app->user->identity->isPatient();
                            },
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        $patientId = $user->patient_id;
        
        $searchModel = new AppointmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->andWhere(['patient_id' => $patientId]);
        $dataProvider->query->orderBy(['appointment_date' => SORT_DESC, 'appointment_time' => SORT_DESC]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($appt_id)
    {
        $model = $this->findModel($appt_id);
        $user = Yii::$app->user->identity;
        
        if ($model->patient_id !== $user->patient_id) {
            throw new \yii\web\ForbiddenHttpException('You can only view your own appointments.');
        }
        
        return $this->render('view', ['model' => $model]);
    }

    public function actionCreate()
    {
        $model = new TblAppointment();
        $user = Yii::$app->user->identity;
        $patient = TblPatient::findOne($user->patient_id);
        
        if (!$patient) {
            Yii::$app->session->setFlash('error', 'Patient profile not found.');
            return $this->redirect(['site/index']);
        }

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                $model->patient_id = $user->patient_id;
                $model->status = null;
                $model->recep_id = null;
                $model->appointment_date = null;
                $model->appointment_time = null;
                
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', '✅ Appointment request submitted! The receptionist will schedule your appointment.');
                    return $this->redirect(['view', 'appt_id' => $model->appt_id]);
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
            'patient' => $patient,
        ]);
    }

    protected function findModel($appt_id)
    {
        if (($model = TblAppointment::findOne(['appt_id' => $appt_id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}