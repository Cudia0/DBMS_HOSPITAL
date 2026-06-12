<?php

namespace frontend\controllers;

use app\models\TblReceptionist;
use app\models\ReceptionistSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ReceptionistController implements the CRUD actions for TblReceptionist model.
 */
class ReceptionistController extends Controller
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
     * Lists all TblReceptionist models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new ReceptionistSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TblReceptionist model.
     * @param int $recep_id Recep ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($recep_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($recep_id),
        ]);
    }

    /**
     * Creates a new TblReceptionist model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new TblReceptionist();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'recep_id' => $model->recep_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TblReceptionist model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $recep_id Recep ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($recep_id)
    {
        $model = $this->findModel($recep_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'recep_id' => $model->recep_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TblReceptionist model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $recep_id Recep ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($recep_id)
    {
        $this->findModel($recep_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the TblReceptionist model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $recep_id Recep ID
     * @return TblReceptionist the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($recep_id)
    {
        if (($model = TblReceptionist::findOne(['recep_id' => $recep_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
