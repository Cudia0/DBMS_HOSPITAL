<?php

namespace backend\controllers;

use common\models\TblMedicalRecord;
use common\models\MedicalRecordSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use Yii;

/**
 * MedicalRecordController - Director & Doctor can manage, Receptionist can view
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
                            'roles' => ['@'],
                            'matchCallback' => function ($rule, $action) {
                                $user = Yii::$app->user->identity;
                                // All staff can view
                                if (in_array($action->id, ['index', 'view'])) {
                                    return $user->isDirector() || $user->isReceptionist() || $user->isDoctor();
                                }
                                // Only Director & Doctor can manage
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

    public function actionIndex()
    {
        $searchModel = new MedicalRecordSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        return $this->render('index', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]);
    }

    public function actionView($record_id)
    {
        return $this->render('view', ['model' => $this->findModel($record_id)]);
    }

    public function actionCreate()
    {
        $model = new TblMedicalRecord();
        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'record_id' => $model->record_id]);
        }
        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate($record_id)
    {
        $model = $this->findModel($record_id);
        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'record_id' => $model->record_id]);
        }
        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete($record_id)
    {
        if (!Yii::$app->user->identity->isDirector()) {
            Yii::$app->session->setFlash('error', 'Only Director can delete medical records.');
            return $this->redirect(['index']);
        }
        $this->findModel($record_id)->delete();
        return $this->redirect(['index']);
    }

    protected function findModel($record_id)
    {
        if (($model = TblMedicalRecord::findOne(['record_id' => $record_id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}