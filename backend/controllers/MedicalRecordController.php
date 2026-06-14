<?php

namespace backend\controllers;

use common\models\TblMedicalRecord;
use common\models\TblAppointment;
use common\models\MedicalRecordSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use Yii;

/**
 * MedicalRecordController - Director & Doctor manage, Receptionist can view
 */
class MedicalRecordController extends Controller
{
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
                            'actions' => ['create', 'update', 'delete'],
                            'roles' => ['@'],
                            'matchCallback' => function ($rule, $action) {
                                $user = Yii::$app->user->identity;
                                return $user->isDirector() || $user->isDoctor();
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
     * Lists all Medical Records
     */
    public function actionIndex()
    {
        $searchModel = new MedicalRecordSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        
        $user = Yii::$app->user->identity;
        
        if ($user->isDoctor()) {
            $dataProvider->query->joinWith('appointment')
                ->andWhere(['tbl_appointment.dr_id' => $user->doctor_id]);
        }
        
        $dataProvider->query->orderBy(['record_date' => SORT_DESC]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Medical Record
     */
    public function actionView($record_id)
    {
        $model = $this->findModel($record_id);
        
        $user = Yii::$app->user->identity;
        if ($user->isDoctor() && $model->appointment && $model->appointment->dr_id !== $user->doctor_id) {
            throw new \yii\web\ForbiddenHttpException('You can only view medical records for your own patients.');
        }
        
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Medical Record - Only 1 per appointment
     */
    public function actionCreate($appt_id = null)
    {
        $model = new TblMedicalRecord();
        $model->record_date = date('Y-m-d H:i:s');
        
        // If appt_id provided, check for existing record
        if ($appt_id) {
            $existingRecord = TblMedicalRecord::find()
                ->where(['appt_id' => $appt_id])
                ->one();
            
            if ($existingRecord) {
                Yii::$app->session->setFlash('warning', 
                    '⚠️ A medical record already exists for this appointment (Record #' . $existingRecord->record_id . '). ' .
                    'You can update the existing record instead.'
                );
                return $this->redirect(['update', 'record_id' => $existingRecord->record_id]);
            }
            
            $model->appt_id = $appt_id;
        }

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                // Check again for existing record before saving
                $existingRecord = TblMedicalRecord::find()
                    ->where(['appt_id' => $model->appt_id])
                    ->andFilterWhere(['!=', 'record_id', $model->record_id])
                    ->one();
                
                if ($existingRecord) {
                    Yii::$app->session->setFlash('error', 
                        '❌ A medical record already exists for this appointment. Please update the existing one.'
                    );
                    return $this->redirect(['update', 'record_id' => $existingRecord->record_id]);
                }
                
                $model->record_date = date('Y-m-d H:i:s');
                
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', '✅ Medical record created successfully.');
                    return $this->redirect(['view', 'record_id' => $model->record_id]);
                }
            }
        }

        return $this->render('create', ['model' => $model]);
    }

    /**
     * Updates an existing Medical Record
     */
    public function actionUpdate($record_id)
    {
        $model = $this->findModel($record_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', '✅ Medical record updated successfully.');
            return $this->redirect(['view', 'record_id' => $model->record_id]);
        }

        return $this->render('update', ['model' => $model]);
    }

    /**
     * Deletes a Medical Record (Director only)
     */
    public function actionDelete($record_id)
    {
        if (!Yii::$app->user->identity->isDirector()) {
            Yii::$app->session->setFlash('error', 'Only Director can delete medical records.');
            return $this->redirect(['index']);
        }
        $this->findModel($record_id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * Finds the TblMedicalRecord model
     */
    protected function findModel($record_id)
    {
        if (($model = TblMedicalRecord::findOne(['record_id' => $record_id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}