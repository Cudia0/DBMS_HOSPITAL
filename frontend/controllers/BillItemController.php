<?php

namespace  frontend\controllers;

use common\models\TblBillItem;
use common\models\BillItemSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * BillItemController implements the CRUD actions for TblBillItem model.
 */
class BillItemController extends Controller
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
     * Lists all TblBillItem models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new BillItemSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TblBillItem model.
     * @param int $bill_item_id Bill Item ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($bill_item_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($bill_item_id),
        ]);
    }

    /**
     * Creates a new TblBillItem model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new TblBillItem();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'bill_item_id' => $model->bill_item_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TblBillItem model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $bill_item_id Bill Item ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($bill_item_id)
    {
        $model = $this->findModel($bill_item_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'bill_item_id' => $model->bill_item_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TblBillItem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $bill_item_id Bill Item ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($bill_item_id)
    {
        $this->findModel($bill_item_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the TblBillItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $bill_item_id Bill Item ID
     * @return TblBillItem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($bill_item_id)
    {
        if (($model = TblBillItem::findOne(['bill_item_id' => $bill_item_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
