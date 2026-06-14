<?php

namespace frontend\controllers;

use common\repositories\BillRepository;
use common\repositories\BillItemRepository;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ArrayDataProvider;

/**
 * BillController - Patient VIEW ONLY (Frontend)
 * Uses raw SQL via repositories
 */
class BillController extends Controller
{
    private BillRepository $billRepo;
    private BillItemRepository $billItemRepo;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->billRepo = new BillRepository();
        $this->billItemRepo = new BillItemRepository();
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
                                return Yii::$app->user->identity->isPatient();
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
     * Lists patient's own bills
     * SQL: SELECT b.* FROM tbl_bill b JOIN tbl_appointment a ON b.appt_id = a.appt_id WHERE a.patient_id = :patient_id ORDER BY b.bill_date DESC
     */
    public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        
        // SQL: SELECT b.*, a.appointment_date, d.last_name FROM tbl_bill b JOIN tbl_appointment a ON b.appt_id = a.appt_id JOIN tbl_doctor d ON a.dr_id = d.dr_id WHERE a.patient_id = :patient_id
        $bills = $this->billRepo->findByPatient($user->patient_id);
        
        $dataProvider = new ArrayDataProvider([
            'allModels' => $bills,
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    /**
     * Displays a single bill with items (must be patient's own)
     * SQL: SELECT ... WHERE bill_id = :id
     */
    public function actionView($bill_id)
    {
        // SQL: SELECT ... WHERE bill_id = :id
        $model = $this->billRepo->findById($bill_id);
        
        if (!$model) throw new NotFoundHttpException('Bill not found.');
        
        // SQL: SELECT * FROM tbl_bill_item WHERE bill_id = :bill_id
        $billItems = $this->billItemRepo->findByBill($bill_id);

        return $this->render('view', [
            'model' => (object) $model,
            'billItems' => $billItems,
        ]);
    }
}