<?php

namespace frontend\controllers;

use common\models\TblMedicalRecord;
use common\models\MedicalRecordSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use Yii;

/**
 * MedicalRecordController - Patient VIEW ONLY
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
        
        $searchModel = new MedicalRecordSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->joinWith('appointment')
            ->andWhere(['tbl_appointment.patient_id' => $patientId]);
        $dataProvider->query->orderBy(['record_date' => SORT_DESC]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($record_id)
    {
        $model = $this->findModel($record_id);
        $user = Yii::$app->user->identity;
        
        if (!$model->appointment || $model->appointment->patient_id !== $user->patient_id) {
            throw new \yii\web\ForbiddenHttpException('You can only view your own medical records.');
        }
        
        return $this->render('view', ['model' => $model]);
    }

    protected function findModel($record_id)
    {
        if (($model = TblMedicalRecord::findOne(['record_id' => $record_id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}