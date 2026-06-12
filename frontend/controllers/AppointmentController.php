<?php

namespace frontend\controllers;

use app\models\TblAppointment;
use app\models\AppointmentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AppointmentController implements the CRUD actions for TblAppointment model.
 */
class AppointmentController extends Controller
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
     * Lists all TblAppointment models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new AppointmentSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TblAppointment model.
     * @param int $appt_id Appt ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($appt_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($appt_id),
        ]);
    }

    /**
     * Creates a new TblAppointment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new TblAppointment();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'appt_id' => $model->appt_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TblAppointment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $appt_id Appt ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($appt_id)
    {
        $model = $this->findModel($appt_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'appt_id' => $model->appt_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TblAppointment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $appt_id Appt ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($appt_id)
    {
        $this->findModel($appt_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the TblAppointment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $appt_id Appt ID
     * @return TblAppointment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($appt_id)
    {
        if (($model = TblAppointment::findOne(['appt_id' => $appt_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
