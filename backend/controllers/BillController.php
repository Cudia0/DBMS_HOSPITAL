<?php

namespace backend\controllers;

use common\repositories\BillRepository;
use common\repositories\BillItemRepository;
use common\repositories\AppointmentRepository;
use common\repositories\PrescriptionRepository;
use common\repositories\LabTestRepository;
use common\repositories\DoctorRepository;
use common\repositories\MedicineRepository;
use common\repositories\MedlineRepository;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ArrayDataProvider;

/**
 * BillController - Bills are AUTO-GENERATED
 * Receptionist: View bills, Process payments, Print receipts
 * Director: Full access
 * Doctor: View only
 * Uses raw SQL via repositories
 */
class BillController extends Controller
{
    private BillRepository $billRepo;
    private BillItemRepository $billItemRepo;
    private AppointmentRepository $appointmentRepo;
    private PrescriptionRepository $prescriptionRepo;
    private LabTestRepository $labTestRepo;
    private DoctorRepository $doctorRepo;
    private MedicineRepository $medicineRepo;
    private MedlineRepository $medlineRepo;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->billRepo = new BillRepository();
        $this->billItemRepo = new BillItemRepository();
        $this->appointmentRepo = new AppointmentRepository();
        $this->prescriptionRepo = new PrescriptionRepository();
        $this->labTestRepo = new LabTestRepository();
        $this->doctorRepo = new DoctorRepository();
        $this->medicineRepo = new MedicineRepository();
        $this->medlineRepo = new MedlineRepository();
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
                            'actions' => ['index', 'view', 'print'],
                            'roles' => ['@'],
                            'matchCallback' => function ($rule, $action) {
                                $user = Yii::$app->user->identity;
                                return $user->isDirector() || $user->isReceptionist() || $user->isDoctor();
                            },
                        ],
                        [
                            'allow' => true,
                            'actions' => ['mark-paid'],
                            'roles' => ['@'],
                            'matchCallback' => function ($rule, $action) {
                                $user = Yii::$app->user->identity;
                                return $user->isDirector() || $user->isReceptionist();
                            },
                        ],
                        [
                            'allow' => true,
                            'actions' => ['create', 'update', 'delete', 'calculate'],
                            'roles' => ['@'],
                            'matchCallback' => function ($rule, $action) {
                                return Yii::$app->user->identity->isDirector();
                            },
                        ],
                    ],
                ],
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => ['delete' => ['POST'], 'mark-paid' => ['POST']],
                ],
            ]
        );
    }

    /**
     * Lists all bills
     */
    public function actionIndex()
    {
        $bills = $this->billRepo->findAll();
        
        $dataProvider = new ArrayDataProvider([
            'allModels' => $bills,
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    /**
     * Displays a single bill with all items
     */
    public function actionView($bill_id)
    {
        $model = $this->billRepo->findById($bill_id);
        if (!$model) throw new NotFoundHttpException('Bill not found.');
        
        $billItems = $this->billItemRepo->findByBill($bill_id);

        return $this->render('view', [
            'model' => (object) $model,
            'billItems' => $billItems,
        ]);
    }

    /**
     * Print bill as PDF receipt
     */
    public function actionPrint($bill_id)
    {
        $model = $this->billRepo->findById($bill_id);
        if (!$model) throw new NotFoundHttpException('Bill not found.');
        
        $billItems = $this->billItemRepo->findByBill($bill_id);
        $appointment = $this->appointmentRepo->findById($model['appt_id']);
        $prescription = $this->prescriptionRepo->findByAppointment($model['appt_id']);
        $labTests = $this->labTestRepo->findByAppointment($model['appt_id']);
        
        $content = $this->renderPartial('print', [
            'model' => (object) $model,
            'billItems' => $billItems,
            'patient' => $appointment ? (object) $appointment : null,
            'doctor' => $appointment ? (object) $appointment : null,
            'appointment' => $appointment ? (object) $appointment : null,
            'prescription' => $prescription ? (object) $prescription : null,
            'labTests' => $labTests,
        ]);
        
        $tmpDir = Yii::getAlias('@runtime/mpdf');
        if (!is_dir($tmpDir)) mkdir($tmpDir, 0775, true);
        
        $pdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8', 'format' => 'A4',
            'margin_left' => 10, 'margin_right' => 10,
            'margin_top' => 10, 'margin_bottom' => 10,
            'tempDir' => $tmpDir,
        ]);
        
        $pdf->SetWatermarkText('MediSync', 0.04);
        $pdf->showWatermarkText = true;
        $pdf->watermarkTextAlpha = 0.04;
        $pdf->SetTitle('Receipt - Bill #' . $model['bill_id'] . ' - MediSync');
        $pdf->SetAuthor('MediSync Hospital');
        $pdf->WriteHTML($content);
        $pdf->Output('Receipt_Bill_' . $model['bill_id'] . '.pdf', 'I');
        exit;
    }

    /**
     * Calculate bill totals from appointment
     */
    public function actionCalculate($appt_id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $appointment = $this->appointmentRepo->findById($appt_id);
        if (!$appointment) return ['success' => false];
        
        $doctor = $this->doctorRepo->findById($appointment['dr_id']);
        $dr_fee = $doctor ? $doctor['dr_fee'] : 0;
        
        $medicine_total = 0;
        $prescriptions = $this->prescriptionRepo->findByAppointment($appt_id);
        if ($prescriptions) {
            $medlines = $this->medlineRepo->findByPrescription($prescriptions['prescription_id']);
            foreach ($medlines as $medline) {
                $med = $this->medicineRepo->findById($medline['med_id']);
                if ($med) {
                    $medicine_total += ($med['med_price'] * $medline['qty']);
                }
            }
        }
        
        return [
            'success' => true,
            'dr_fee' => $dr_fee,
            'medicine_total' => $medicine_total,
            'grand_total' => $dr_fee + $medicine_total
        ];
    }

    /**
     * Creates a bill manually (Director only)
     * SQL: INSERT INTO tbl_bill (...) VALUES (...)
     */
    public function actionCreate()
    {
        $model = new \common\models\TblBill();
        $model->payment_status = 'pending';
        $model->bill_date = date('Y-m-d H:i:s');

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post('TblBill', []);
            $post['bill_date'] = date('Y-m-d H:i:s');
            $post['payment_status'] = $post['payment_status'] ?? 'pending';
            $post['dr_fee'] = $post['dr_fee'] ?? 0;
            $post['totalm_price'] = $post['totalm_price'] ?? 0;
            $post['total_amount'] = ($post['dr_fee'] ?? 0) + ($post['totalm_price'] ?? 0);
            
            // SQL: INSERT INTO tbl_bill (...) VALUES (...)
            $id = $this->billRepo->create($post);
            
            if ($id) {
                Yii::$app->session->setFlash('success', '✅ Bill #' . $id . ' created successfully. You can now add charges to this bill.');
                return $this->redirect(['view', 'bill_id' => $id]);
            } else {
                Yii::$app->session->setFlash('error', '❌ Failed to create bill.');
            }
        }

        return $this->render('create', ['model' => $model]);
    }

    /**
     * Updates a bill (Director only)
     * SQL: UPDATE tbl_bill SET ... WHERE bill_id = :id
     */
    public function actionUpdate($bill_id)
    {
        $bill = $this->billRepo->findById($bill_id);
        if (!$bill) throw new NotFoundHttpException('Bill not found.');

        $model = new \common\models\TblBill();
        $model->attributes = $bill;

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post('TblBill', []);
            $post['total_amount'] = ($post['dr_fee'] ?? 0) + ($post['totalm_price'] ?? 0);
            
            // SQL: UPDATE tbl_bill SET ... WHERE bill_id = :id
            $this->billRepo->update($bill_id, $post);
            
            Yii::$app->session->setFlash('success', '✅ Bill #' . $bill_id . ' updated successfully.');
            return $this->redirect(['view', 'bill_id' => $bill_id]);
        }

        return $this->render('update', ['model' => $model]);
    }

    /**
     * Mark bill as paid (Receptionist)
     * SQL: UPDATE tbl_bill SET payment_status = 'paid', payment_method = :method WHERE bill_id = :id
     * SQL: UPDATE tbl_appointment SET status = 'completed' WHERE appt_id = :id
     */
    public function actionMarkPaid($bill_id)
    {
        $bill = $this->billRepo->findById($bill_id);
        if (!$bill) throw new NotFoundHttpException('Bill not found.');
        
        $user = Yii::$app->user->identity;
        
        if (!$user->isDirector() && !$user->isReceptionist()) {
            Yii::$app->session->setFlash('error', 'You do not have permission.');
            return $this->redirect(['view', 'bill_id' => $bill_id]);
        }
        
        if ($bill['payment_status'] === 'pending' || $bill['payment_status'] === 'partial') {
            if (Yii::$app->request->isPost) {
                $method = Yii::$app->request->post('payment_method', $bill['payment_method']);
                
                // SQL: UPDATE tbl_bill SET payment_status = 'paid', payment_method = :method WHERE bill_id = :id
                $this->billRepo->markAsPaid($bill_id, 'paid', $method);
                
                // SQL: UPDATE tbl_appointment SET status = 'completed' WHERE appt_id = :id
                $appointment = $this->appointmentRepo->findById($bill['appt_id']);
                if ($appointment && $appointment['status'] === 'in_progress') {
                    $this->appointmentRepo->updateStatus($bill['appt_id'], 'completed');
                }
                
                Yii::$app->session->setFlash('success', '✅ Payment processed! Amount: ₱' . number_format($bill['total_amount'], 2));
            }
        } else {
            Yii::$app->session->setFlash('warning', 'Bill is already marked as ' . $bill['payment_status'] . '.');
        }
        
        return $this->redirect(['view', 'bill_id' => $bill_id]);
    }

    /**
     * Deletes a bill (Director only)
     * SQL: DELETE FROM tbl_bill WHERE bill_id = :id
     */
    public function actionDelete($bill_id)
    {
        if (!Yii::$app->user->identity->isDirector()) {
            Yii::$app->session->setFlash('error', 'Only Director can delete bills.');
            return $this->redirect(['index']);
        }
        
        // SQL: DELETE FROM tbl_bill WHERE bill_id = :id
        $this->billRepo->delete($bill_id);
        
        Yii::$app->session->setFlash('success', 'Bill deleted.');
        return $this->redirect(['index']);
    }
}