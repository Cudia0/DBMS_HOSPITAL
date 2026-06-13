<?php

namespace frontend\controllers;

use common\models\TblAppointment;
use common\models\AppointmentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use Yii;

/**
 * AppointmentController - Frontend (Patient Only)
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
    
    /**
     * @inheritDoc
     */
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
                            'actions' => ['index', 'view', 'create'], // Patients can view and create
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

    /**
     * Lists all TblAppointment models (patient sees only their own).
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new AppointmentSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        
        // Filter to show only patient's own appointments
        $patientId = Yii::$app->user->identity->patient_id;
        if ($patientId) {
            $dataProvider->query->andWhere(['patient_id' => $patientId]);
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TblAppointment model.
     * @param int $appt_id Appt ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($appt_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($appt_id),
        ]);
    }

    /**
     * Creates a new TblAppointment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new TblAppointment();
        
        // Auto-set patient_id from logged-in user
        $patientId = Yii::$app->user->identity->patient_id;
        if ($patientId) {
            $model->patient_id = $patientId;
        }

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                $model->patient_id = $patientId; // Ensure patient_id is set
                $model->status = 'scheduled'; // Default status
                
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Appointment booked successfully!');
                    return $this->redirect(['view', 'appt_id' => $model->appt_id]);
                }
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the TblAppointment model based on its primary key value.
     * @param int $appt_id Appt ID
     * @return TblAppointment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($appt_id)
    {
        if (($model = TblAppointment::findOne(['appt_id' => $appt_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}