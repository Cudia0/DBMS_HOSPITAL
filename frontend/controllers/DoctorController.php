<?php

namespace frontend\controllers;

use app\models\TblDoctor;
use app\models\DoctorSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DoctorController implements the CRUD actions for TblDoctor model.
 */
class DoctorController extends Controller
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
     * Lists all TblDoctor models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new DoctorSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TblDoctor model.
     * @param int $dr_id Dr ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($dr_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($dr_id),
        ]);
    }

    /**
     * Creates a new TblDoctor model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new TblDoctor();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'dr_id' => $model->dr_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TblDoctor model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $dr_id Dr ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($dr_id)
    {
        $model = $this->findModel($dr_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'dr_id' => $model->dr_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TblDoctor model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $dr_id Dr ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($dr_id)
    {
        $this->findModel($dr_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the TblDoctor model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $dr_id Dr ID
     * @return TblDoctor the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($dr_id)
    {
        if (($model = TblDoctor::findOne(['dr_id' => $dr_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
