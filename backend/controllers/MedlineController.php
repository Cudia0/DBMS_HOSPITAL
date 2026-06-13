<?php

namespace backend\controllers;

use common\models\TblMedline;
use common\models\MedlineSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * MedlineController implements the CRUD actions for TblMedline model.
 */
class MedlineController extends Controller
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
     * Lists all TblMedline models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new MedlineSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TblMedline model.
     * @param int $medline_id Medline ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($medline_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($medline_id),
        ]);
    }

    /**
     * Creates a new TblMedline model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new TblMedline();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'medline_id' => $model->medline_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TblMedline model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $medline_id Medline ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($medline_id)
    {
        $model = $this->findModel($medline_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'medline_id' => $model->medline_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TblMedline model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $medline_id Medline ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($medline_id)
    {
        $this->findModel($medline_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the TblMedline model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $medline_id Medline ID
     * @return TblMedline the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($medline_id)
    {
        if (($model = TblMedline::findOne(['medline_id' => $medline_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
