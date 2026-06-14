<?php

namespace frontend\controllers;

use common\models\TblBill;
use common\models\BillSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use Yii;

/**
 * BillController - Patient VIEW ONLY
 */
class BillController extends Controller
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
        
        $searchModel = new BillSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->query->joinWith('appointment')
            ->andWhere(['tbl_appointment.patient_id' => $patientId]);
        $dataProvider->query->orderBy(['bill_date' => SORT_DESC]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($bill_id)
    {
        $model = $this->findModel($bill_id);
        $user = Yii::$app->user->identity;
        
        if (!$model->appointment || $model->appointment->patient_id !== $user->patient_id) {
            throw new \yii\web\ForbiddenHttpException('You can only view your own bills.');
        }
        
        return $this->render('view', ['model' => $model]);
    }

    protected function findModel($bill_id)
    {
        if (($model = TblBill::findOne(['bill_id' => $bill_id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}