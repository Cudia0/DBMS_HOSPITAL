<?php

namespace backend\controllers;

use common\models\TblLabTest;
use common\models\TblAppointment;
use common\models\LabTestSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use Yii;

/**
 * LabTestController - Director & Doctor manage, Receptionist can view
 * Lab tests are OPTIONAL diagnostic tests ordered by doctors
 */
class LabTestController extends Controller
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
     * Lists lab tests - Filtered for doctor to show only their patients
     */
    public function actionIndex()
    {
        $searchModel = new LabTestSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        
        $user = Yii::$app->user->identity;
        
        if ($user->isDoctor()) {
            $dataProvider->query->joinWith('appointment')
                ->andWhere(['tbl_appointment.dr_id' => $user->doctor_id]);
        }
        
        $dataProvider->query->orderBy(['ordered_date' => SORT_DESC]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single lab test
     */
    public function actionView($test_id)
    {
        $model = $this->findModel($test_id);
        
        $user = Yii::$app->user->identity;
        if ($user->isDoctor() && $model->appointment && $model->appointment->dr_id !== $user->doctor_id) {
            throw new \yii\web\ForbiddenHttpException('You can only view lab tests for your own patients.');
        }
        
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new lab test order (Doctor only)
     */
    public function actionCreate($appt_id = null)
    {
        $model = new TblLabTest();
        $model->status = 'ordered';
        $model->ordered_date = date('Y-m-d H:i:s');
        
        if ($appt_id) {
            $model->appt_id = $appt_id;
        }

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                $model->ordered_date = date('Y-m-d H:i:s');
                
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', '✅ Lab test ordered successfully.');
                    return $this->redirect(['view', 'test_id' => $model->test_id]);
                }
            }
        }

        return $this->render('create', ['model' => $model]);
    }

    /**
     * Updates a lab test (add results, change status)
     */
    public function actionUpdate($test_id)
    {
        $model = $this->findModel($test_id);

        if ($this->request->isPost && $model->load($this->request->post())) {
            // If results are being filled, auto-set results_date
            if ($model->results && !$model->results_date) {
                $model->results_date = date('Y-m-d H:i:s');
            }
            
            if ($model->save()) {
                Yii::$app->session->setFlash('success', '✅ Lab test updated successfully.');
                return $this->redirect(['view', 'test_id' => $model->test_id]);
            }
        }

        return $this->render('update', ['model' => $model]);
    }

    /**
     * Deletes a lab test (Director only)
     */
    public function actionDelete($test_id)
    {
        if (!Yii::$app->user->identity->isDirector()) {
            Yii::$app->session->setFlash('error', 'Only Director can delete lab tests.');
            return $this->redirect(['index']);
        }
        $this->findModel($test_id)->delete();
        return $this->redirect(['index']);
    }

    protected function findModel($test_id)
    {
        if (($model = TblLabTest::findOne(['test_id' => $test_id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}