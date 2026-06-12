<?php

namespace backend\controllers;

use app\models\TblMedicine;
use app\models\MedicineSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
/**
 * MedicineController implements the CRUD actions for TblMedicine model.
 */
class MedicineController extends Controller
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
     * Lists all TblMedicine models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new MedicineSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TblMedicine model.
     * @param int $med_id Med ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($med_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($med_id),
        ]);
    }

    /**
     * Creates a new TblMedicine model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new TblMedicine();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'med_id' => $model->med_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TblMedicine model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $med_id Med ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($med_id)
    {
        $model = $this->findModel($med_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'med_id' => $model->med_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TblMedicine model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $med_id Med ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($med_id)
    {
        $this->findModel($med_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the TblMedicine model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $med_id Med ID
     * @return TblMedicine the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($med_id)
    {
        if (($model = TblMedicine::findOne(['med_id' => $med_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
