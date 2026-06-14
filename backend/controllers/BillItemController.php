<?php

namespace backend\controllers;

use common\repositories\BillItemRepository;
use common\repositories\BillRepository;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ArrayDataProvider;

/**
 * BillItemController - Director & Receptionist manage bill items
 * Bill Items = Individual charges on a bill
 * Uses raw SQL via repositories
 */
class BillItemController extends Controller
{
    private BillItemRepository $billItemRepo;
    private BillRepository $billRepo;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->billItemRepo = new BillItemRepository();
        $this->billRepo = new BillRepository();
    }

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
                    'actions' => ['delete' => ['POST']],
                ],
            ]
        );
    }

    /**
     * Lists all bill items
     * SQL: SELECT * FROM tbl_bill_item ORDER BY created_at DESC
     */
    public function actionIndex()
    {
        // SQL: SELECT * FROM tbl_bill_item ORDER BY created_at DESC
        $items = $this->billItemRepo->findAll();
        
        $dataProvider = new ArrayDataProvider([
            'allModels' => $items,
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    /**
     * Displays a single bill item
     * SQL: SELECT * FROM tbl_bill_item WHERE bill_item_id = :id
     */
    public function actionView($bill_item_id)
    {
        // SQL: SELECT * FROM tbl_bill_item WHERE bill_item_id = :id
        $model = $this->billItemRepo->findById($bill_item_id);
        
        if (!$model) {
            throw new NotFoundHttpException('Bill item not found.');
        }

        return $this->render('view', ['model' => (object) $model]);
    }

    /**
     * Adds a new charge to a bill
     * SQL: INSERT INTO tbl_bill_item (...) VALUES (...)
     * SQL: UPDATE tbl_bill SET total_amount = ... WHERE bill_id = :id
     */
    public function actionCreate($bill_id = null)
    {
        $model = new \common\models\TblBillItem();
        
        if ($bill_id) {
            $model->bill_id = $bill_id;
            $bill = $this->billRepo->findById($bill_id);
            if (!$bill) {
                Yii::$app->session->setFlash('error', 'Bill not found.');
                return $this->redirect(['bill/index']);
            }
        }

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post('TblBillItem', []);
            $post['total_price'] = ($post['quantity'] ?? 0) * ($post['unit_price'] ?? 0);
            
            // SQL: INSERT INTO tbl_bill_item (...) VALUES (...)
            $id = $this->billItemRepo->create($post);
            
            if ($id) {
                // Recalculate bill total
                $this->recalculateBillTotal($post['bill_id']);
                
                Yii::$app->session->setFlash('success', '✅ Charge added to Bill #' . $post['bill_id']);
                return $this->redirect(['bill/view', 'bill_id' => $post['bill_id']]);
            }
        }

        return $this->render('create', ['model' => $model, 'bill' => $bill ?? null]);
    }

    /**
     * Updates a bill item
     * SQL: UPDATE tbl_bill_item SET ... WHERE bill_item_id = :id
     */
    public function actionUpdate($bill_item_id)
    {
        $item = $this->billItemRepo->findById($bill_item_id);
        if (!$item) throw new NotFoundHttpException('Bill item not found.');

        $model = new \common\models\TblBillItem();
        $model->attributes = $item;
        $bill = $this->billRepo->findById($item['bill_id']);

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post('TblBillItem', []);
            $post['total_price'] = ($post['quantity'] ?? 0) * ($post['unit_price'] ?? 0);
            
            // SQL: UPDATE tbl_bill_item SET ... WHERE bill_item_id = :id
            $this->billItemRepo->update($bill_item_id, $post);
            
            // Recalculate bill total
            $this->recalculateBillTotal($post['bill_id']);
            
            Yii::$app->session->setFlash('success', '✅ Bill item updated.');
            return $this->redirect(['bill/view', 'bill_id' => $post['bill_id']]);
        }

        return $this->render('update', ['model' => $model, 'bill' => $bill]);
    }

    /**
     * Deletes a bill item (Director only)
     * SQL: DELETE FROM tbl_bill_item WHERE bill_item_id = :id
     */
    public function actionDelete($bill_item_id)
    {
        $item = $this->billItemRepo->findById($bill_item_id);
        if (!$item) throw new NotFoundHttpException('Bill item not found.');
        
        $billId = $item['bill_id'];
        
        if (!Yii::$app->user->identity->isDirector()) {
            Yii::$app->session->setFlash('error', 'Only Director can delete bill items.');
            return $this->redirect(['bill/view', 'bill_id' => $billId]);
        }
        
        // SQL: DELETE FROM tbl_bill_item WHERE bill_item_id = :id
        $this->billItemRepo->delete($bill_item_id);
        
        // Recalculate bill total
        $this->recalculateBillTotal($billId);
        
        Yii::$app->session->setFlash('success', '✅ Bill item deleted.');
        return $this->redirect(['bill/view', 'bill_id' => $billId]);
    }

    /**
     * Recalculate bill total from bill items
     * SQL: UPDATE tbl_bill SET dr_fee = ?, totalm_price = ?, total_amount = ? WHERE bill_id = ?
     */
    private function recalculateBillTotal($bill_id)
    {
        // SQL: SELECT COALESCE(SUM(total_price), 0) FROM tbl_bill_item WHERE bill_id = :bill_id
        $totalAmount = $this->billItemRepo->getTotalByBill($bill_id);
        
        // SQL: SELECT COALESCE(SUM(total_price), 0) FROM tbl_bill_item WHERE bill_id = :bill_id AND item_type = 'consultation'
        $consultationTotal = $this->billItemRepo->getTotalByBillAndType($bill_id, 'consultation');
        
        // SQL: SELECT COALESCE(SUM(total_price), 0) FROM tbl_bill_item WHERE bill_id = :bill_id AND item_type = 'medicine'
        $medicineTotal = $this->billItemRepo->getTotalByBillAndType($bill_id, 'medicine');
        
        // SQL: UPDATE tbl_bill SET dr_fee = ?, totalm_price = ?, total_amount = ? WHERE bill_id = ?
        $this->billRepo->updateTotals($bill_id, $consultationTotal, $medicineTotal, $totalAmount);
    }
}