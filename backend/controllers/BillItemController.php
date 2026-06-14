<?php

namespace backend\controllers;

use common\models\TblBillItem;
use common\models\TblBill;
use common\models\BillItemSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\Html;
use Yii;

/**
 * BillItemController - Director & Receptionist manage bill items
 * 
 * Bill Items = Individual charges on a bill (like items on a receipt)
 * Bill Total = Sum of ALL bill items (single source of truth)
 */
class BillItemController extends Controller
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
                            'actions' => ['index', 'view'],
                            'roles' => ['@'],
                            'matchCallback' => function ($rule, $action) {
                                $user = Yii::$app->user->identity;
                                return $user->isDirector() || $user->isReceptionist();
                            },
                        ],
                        [
                            'allow' => true,
                            'actions' => ['create', 'update'],
                            'roles' => ['@'],
                            'matchCallback' => function ($rule, $action) {
                                $user = Yii::$app->user->identity;
                                return $user->isDirector() || $user->isReceptionist();
                            },
                        ],
                        [
                            'allow' => true,
                            'actions' => ['delete'],
                            'roles' => ['@'],
                            'matchCallback' => function ($rule, $action) {
                                return Yii::$app->user->identity->isDirector();
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
        $searchModel = new BillItemSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->query->orderBy(['created_at' => SORT_DESC]);
        return $this->render('index', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]);
    }

    public function actionView($bill_item_id)
    {
        return $this->render('view', ['model' => $this->findModel($bill_item_id)]);
    }

    public function actionCreate($bill_id = null)
    {
        $model = new TblBillItem();
        
        if ($bill_id) {
            $model->bill_id = $bill_id;
            $bill = TblBill::findOne($bill_id);
            if (!$bill) {
                Yii::$app->session->setFlash('error', 'Bill not found.');
                return $this->redirect(['bill/index']);
            }
        }

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                $model->total_price = $model->quantity * $model->unit_price;
                
                if ($model->save()) {
                    $this->recalculateBillTotal($model->bill_id);
                    
                    Yii::$app->session->setFlash('success', 
                        '✅ Charge added to Bill #' . $model->bill_id . '<br>' .
                        '<small>Item: ' . Html::encode($model->description) . ' | Amount: ₱' . number_format($model->total_price, 2) . '</small>'
                    );
                    return $this->redirect(['bill/view', 'bill_id' => $model->bill_id]);
                }
            }
        }

        return $this->render('create', ['model' => $model, 'bill' => $bill ?? null]);
    }

    public function actionUpdate($bill_item_id)
    {
        $model = $this->findModel($bill_item_id);
        $bill = TblBill::findOne($model->bill_id);

        if ($this->request->isPost && $model->load($this->request->post())) {
            $model->total_price = $model->quantity * $model->unit_price;
            
            if ($model->save()) {
                $this->recalculateBillTotal($model->bill_id);
                Yii::$app->session->setFlash('success', '✅ Bill item updated successfully.');
                return $this->redirect(['bill/view', 'bill_id' => $model->bill_id]);
            }
        }

        return $this->render('update', ['model' => $model, 'bill' => $bill]);
    }

    public function actionDelete($bill_item_id)
    {
        $model = $this->findModel($bill_item_id);
        $billId = $model->bill_id;
        
        if (!Yii::$app->user->identity->isDirector()) {
            Yii::$app->session->setFlash('error', 'Only Director can delete bill items.');
            return $this->redirect(['bill/view', 'bill_id' => $billId]);
        }
        
        $model->delete();
        $this->recalculateBillTotal($billId);
        
        Yii::$app->session->setFlash('success', '✅ Bill item deleted.');
        return $this->redirect(['bill/view', 'bill_id' => $billId]);
    }

    /**
     * Recalculate bill total FROM BILL ITEMS ONLY (single source of truth)
     */
    public function recalculateBillTotal($bill_id)
    {
        $bill = TblBill::findOne($bill_id);
        if (!$bill) return;
        
        // Sum ALL bill items
        $totalAmount = TblBillItem::find()
            ->where(['bill_id' => $bill_id])
            ->sum('total_price') ?? 0;
        
        // Sum by type
        $consultationTotal = TblBillItem::find()
            ->where(['bill_id' => $bill_id, 'item_type' => 'consultation'])
            ->sum('total_price') ?? 0;
        
        $medicineTotal = TblBillItem::find()
            ->where(['bill_id' => $bill_id, 'item_type' => 'medicine'])
            ->sum('total_price') ?? 0;
        
        // Update bill
        $bill->dr_fee = $consultationTotal;
        $bill->totalm_price = $medicineTotal;
        $bill->total_amount = $totalAmount;
        $bill->save();
    }

    protected function findModel($bill_item_id)
    {
        if (($model = TblBillItem::findOne(['bill_item_id' => $bill_item_id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}