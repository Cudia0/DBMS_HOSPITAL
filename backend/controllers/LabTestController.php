<?php

namespace backend\controllers;

use common\models\TblLabTest;
use common\models\LabTestSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LabTestController implements the CRUD actions for TblLabTest model.
 */
class LabTestController extends Controller
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
     * Lists all TblLabTest models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new LabTestSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TblLabTest model.
     * @param int $test_id Test ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($test_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($test_id),
        ]);
    }

    /**
     * Creates a new TblLabTest model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new TblLabTest();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'test_id' => $model->test_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TblLabTest model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $test_id Test ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($test_id)
    {
        $model = $this->findModel($test_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'test_id' => $model->test_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TblLabTest model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $test_id Test ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($test_id)
    {
        $this->findModel($test_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the TblLabTest model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $test_id Test ID
     * @return TblLabTest the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($test_id)
    {
        if (($model = TblLabTest::findOne(['test_id' => $test_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
