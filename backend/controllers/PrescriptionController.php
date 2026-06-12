<?php

namespace backend\controllers;

use app\models\TblPrescription;
use app\models\PrescriptionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
/**
 * PrescriptionController implements the CRUD actions for TblPrescription model.
 */
class PrescriptionController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'create', 'update', 'view', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
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
     * Lists all TblPrescription models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new PrescriptionSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TblPrescription model.
     * @param int $prescription_id Prescription ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($prescription_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($prescription_id),
        ]);
    }

    /**
     * Creates a new TblPrescription model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new TblPrescription();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'prescription_id' => $model->prescription_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TblPrescription model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $prescription_id Prescription ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($prescription_id)
    {
        $model = $this->findModel($prescription_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'prescription_id' => $model->prescription_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TblPrescription model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $prescription_id Prescription ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($prescription_id)
    {
        $this->findModel($prescription_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the TblPrescription model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $prescription_id Prescription ID
     * @return TblPrescription the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($prescription_id)
    {
        if (($model = TblPrescription::findOne(['prescription_id' => $prescription_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
