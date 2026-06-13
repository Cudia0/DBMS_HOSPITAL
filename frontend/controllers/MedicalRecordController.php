<?php

namespace frontend\controllers;

use common\models\TblMedicalRecord;
use common\models\MedicalRecordSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * MedicalRecordController implements the CRUD actions for TblMedicalRecord model.
 */
class MedicalRecordController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all TblMedicalRecord models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new MedicalRecordSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TblMedicalRecord model.
     * @param int $record_id Record ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($record_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($record_id),
        ]);
    }

    /**
     * Creates a new TblMedicalRecord model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new TblMedicalRecord();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'record_id' => $model->record_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TblMedicalRecord model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $record_id Record ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($record_id)
    {
        $model = $this->findModel($record_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'record_id' => $model->record_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TblMedicalRecord model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $record_id Record ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($record_id)
    {
        $this->findModel($record_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the TblMedicalRecord model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $record_id Record ID
     * @return TblMedicalRecord the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($record_id)
    {
        if (($model = TblMedicalRecord::findOne(['record_id' => $record_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
