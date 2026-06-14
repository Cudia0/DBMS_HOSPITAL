<?php

namespace backend\controllers;

use common\repositories\PrescriptionRepository;
use common\repositories\MedlineRepository;
use common\repositories\AppointmentRepository;
use common\repositories\DoctorRepository;
use common\repositories\MedicineRepository;
use common\repositories\BillRepository;
use common\repositories\BillItemRepository;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\data\ArrayDataProvider;

/**
 * PrescriptionController - Director & Doctor manage, Receptionist can view
 * Auto-generates bill when prescription is created
 * Uses raw SQL via repositories
 */
class PrescriptionController extends Controller
{
    private PrescriptionRepository $prescriptionRepo;
    private MedlineRepository $medlineRepo;
    private AppointmentRepository $appointmentRepo;
    private DoctorRepository $doctorRepo;
    private MedicineRepository $medicineRepo;
    private BillRepository $billRepo;
    private BillItemRepository $billItemRepo;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->prescriptionRepo = new PrescriptionRepository();
        $this->medlineRepo = new MedlineRepository();
        $this->appointmentRepo = new AppointmentRepository();
        $this->doctorRepo = new DoctorRepository();
        $this->medicineRepo = new MedicineRepository();
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
                                $user = Yii::$app->user->identity;
                                return $user->isDirector() || $user->isReceptionist() || $user->isDoctor();
                            },
                        ],
                        [
                            'allow' => true,
                            'actions' => ['create', 'update', 'delete'],
                            'roles' => ['@'],
                            'matchCallback' => function ($rule, $action) {
                                $user = Yii::$app->user->identity;
                                return $user->isDirector() || $user->isDoctor();
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
     * Lists prescriptions - Filtered for doctor
     */
    public function actionIndex()
    {
        $user = Yii::$app->user->identity;
        
        if ($user->isDoctor()) {
            $prescriptions = $this->prescriptionRepo->findByDoctor($user->doctor_id);
        } else {
            $prescriptions = $this->prescriptionRepo->findAll();
        }
        
        $dataProvider = new ArrayDataProvider([
            'allModels' => $prescriptions,
            'pagination' => ['pageSize' => 20],
        ]);

        return $this->render('index', ['dataProvider' => $dataProvider]);
    }

    /**
     * Displays a single prescription with medicines
     */
    public function actionView($prescription_id)
    {
        $model = $this->prescriptionRepo->findById($prescription_id);
        
        if (!$model) {
            throw new NotFoundHttpException('Prescription not found.');
        }
        
        $user = Yii::$app->user->identity;
        if ($user->isDoctor() && isset($model['doctor_lname']) === false) {
            throw new \yii\web\ForbiddenHttpException('You can only view your own prescriptions.');
        }

        return $this->render('view', ['model' => (object) $model]);
    }

    /**
     * Creates a new prescription + Auto-generates bill
     * SQL: INSERT INTO tbl_prescription (...) VALUES (...)
     * SQL: INSERT INTO tbl_medline (...) VALUES (...)
     * SQL: INSERT INTO tbl_bill (...) VALUES (...)
     * SQL: INSERT INTO tbl_bill_item (...) VALUES (...)
     */
    public function actionCreate($appt_id = null)
    {
        $model = new \common\models\TblPrescription();
        
        if ($appt_id) {
            // SQL: SELECT * FROM tbl_prescription WHERE appt_id = :appt_id
            $existing = $this->prescriptionRepo->findByAppointment($appt_id);
            
            if ($existing) {
                Yii::$app->session->setFlash('warning', '⚠️ Prescription already exists for this appointment (Prescription #' . $existing['prescription_id'] . '). You can update it.');
                return $this->redirect(['update', 'prescription_id' => $existing['prescription_id']]);
            }
            
            $model->appt_id = $appt_id;
        }

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post('TblPrescription', []);
            $medicines = Yii::$app->request->post('medicines', []);
            
            // SQL: Check existing again before saving
            if (!empty($post['appt_id'])) {
                $existing = $this->prescriptionRepo->findByAppointment($post['appt_id']);
                if ($existing) {
                    Yii::$app->session->setFlash('error', '❌ Prescription already exists for this appointment.');
                    return $this->redirect(['update', 'prescription_id' => $existing['prescription_id']]);
                }
            }
            
            $post['prescription_date'] = date('Y-m-d H:i:s');
            
            // SQL: INSERT INTO tbl_prescription (...) VALUES (...)
            $prescriptionId = $this->prescriptionRepo->create($post);
            
            if ($prescriptionId) {
                $totalMedicinePrice = 0;
                
                // SQL: INSERT INTO tbl_medline (...) VALUES (...) (for each medicine)
                foreach ($medicines as $medicine) {
                    if (!empty($medicine['med_id']) && !empty($medicine['qty']) && $medicine['qty'] > 0) {
                        // SQL: INSERT INTO tbl_medline (prescription_id, med_id, qty, dosage_per_intake, frequency) VALUES (...)
                        $this->medlineRepo->create([
                            'prescription_id' => $prescriptionId,
                            'med_id' => $medicine['med_id'],
                            'qty' => $medicine['qty'],
                            'dosage_per_intake' => $medicine['dosage'] ?? null,
                            'frequency' => $medicine['frequency'] ?? null,
                        ]);
                        
                        // SQL: SELECT med_price FROM tbl_medicine WHERE med_id = :id
                        $med = $this->medicineRepo->findById($medicine['med_id']);
                        if ($med) {
                            $totalMedicinePrice += ($med['med_price'] * $medicine['qty']);
                        }
                    }
                }
                
                // AUTO-GENERATE BILL
                // SQL: SELECT * FROM tbl_bill WHERE appt_id = :appt_id
                $existingBill = $this->billRepo->findByAppointment($post['appt_id']);
                
                if (!$existingBill) {
                    $this->generateBill($prescriptionId, $post['appt_id']);
                }
                
                Yii::$app->session->setFlash('success', '✅ Prescription created! Bill auto-generated.');
                return $this->redirect(['view', 'prescription_id' => $prescriptionId]);
            }
        }

        return $this->render('create', ['model' => $model]);
    }

    /**
     * Updates a prescription + Updates bill
     */
    public function actionUpdate($prescription_id)
    {
        $prescription = $this->prescriptionRepo->findById($prescription_id);
        if (!$prescription) throw new NotFoundHttpException('Prescription not found.');

        $model = new \common\models\TblPrescription();
        $model->attributes = $prescription;

        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post('TblPrescription', []);
            $medicines = Yii::$app->request->post('medicines', []);
            
            // SQL: UPDATE tbl_prescription SET ... WHERE prescription_id = :id
            $this->prescriptionRepo->update($prescription_id, $post);
            
            // SQL: DELETE FROM tbl_medline WHERE prescription_id = :id
            $this->medlineRepo->deleteByPrescription($prescription_id);
            
            // SQL: INSERT INTO tbl_medline (...) VALUES (...) (for each medicine)
            foreach ($medicines as $medicine) {
                if (!empty($medicine['med_id']) && !empty($medicine['qty']) && $medicine['qty'] > 0) {
                    // SQL: INSERT INTO tbl_medline (prescription_id, med_id, qty, dosage_per_intake, frequency) VALUES (...)
                    $this->medlineRepo->create([
                        'prescription_id' => $prescription_id,
                        'med_id' => $medicine['med_id'],
                        'qty' => $medicine['qty'],
                        'dosage_per_intake' => $medicine['dosage'] ?? null,
                        'frequency' => $medicine['frequency'] ?? null,
                    ]);
                }
            }
            
            // Update bill
            $this->updateBill($prescription_id, $prescription['appt_id']);
            
            Yii::$app->session->setFlash('success', '✅ Prescription updated! Bill updated.');
            return $this->redirect(['view', 'prescription_id' => $prescription_id]);
        }

        return $this->render('update', ['model' => $model]);
    }

    /**
     * Deletes a prescription (Director only)
     * SQL: DELETE FROM tbl_prescription WHERE prescription_id = :id
     */
    public function actionDelete($prescription_id)
    {
        if (!Yii::$app->user->identity->isDirector()) {
            Yii::$app->session->setFlash('error', 'Only Director can delete prescriptions.');
            return $this->redirect(['index']);
        }
        
        // SQL: DELETE FROM tbl_prescription WHERE prescription_id = :id
        $this->prescriptionRepo->delete($prescription_id);
        
        return $this->redirect(['index']);
    }

    /**
     * Auto-generate bill from prescription
     * SQL: INSERT INTO tbl_bill (...) VALUES (...)
     * SQL: INSERT INTO tbl_bill_item (...) VALUES (...)
     */
    private function generateBill($prescriptionId, $apptId)
    {
        // SQL: SELECT a.*, d.dr_fee FROM tbl_appointment a JOIN tbl_doctor d ON a.dr_id = d.dr_id WHERE a.appt_id = :id
        $appointment = $this->appointmentRepo->findById($apptId);
        if (!$appointment) return;
        
        // SQL: SELECT dr_fee FROM tbl_doctor WHERE dr_id = :id
        $doctor = $this->doctorRepo->findById($appointment['dr_id']);
        $drFee = $doctor ? $doctor['dr_fee'] : 0;
        
        // SQL: INSERT INTO tbl_bill (appt_id, payment_status, dr_fee, totalm_price, total_amount, bill_date) VALUES (...)
        $billId = $this->billRepo->create([
            'appt_id' => $apptId,
            'payment_status' => 'pending',
            'dr_fee' => 0,
            'totalm_price' => 0,
            'total_amount' => 0,
            'bill_date' => date('Y-m-d H:i:s'),
        ]);
        
        // SQL: INSERT INTO tbl_bill_item (bill_id, item_type, description, quantity, unit_price, total_price) VALUES (...)
        if ($drFee > 0) {
            $this->billItemRepo->create([
                'bill_id' => $billId,
                'item_type' => 'consultation',
                'description' => 'Doctor Consultation Fee - Dr. ' . ($doctor['last_name'] ?? 'N/A'),
                'quantity' => 1,
                'unit_price' => $drFee,
                'total_price' => $drFee,
            ]);
        }
        
        // SQL: INSERT INTO tbl_bill_item (...) VALUES (...) (for each medicine)
        $medlines = $this->medlineRepo->findByPrescription($prescriptionId);
        foreach ($medlines as $medline) {
            $med = $this->medicineRepo->findById($medline['med_id']);
            if ($med) {
                $itemTotal = $med['med_price'] * $medline['qty'];
                $this->billItemRepo->create([
                    'bill_id' => $billId,
                    'item_type' => 'medicine',
                    'description' => $med['med_name'] . ' (' . $med['strength'] . ') x' . $medline['qty'],
                    'reference_id' => $med['med_id'],
                    'quantity' => $medline['qty'],
                    'unit_price' => $med['med_price'],
                    'total_price' => $itemTotal,
                ]);
            }
        }
        
        // Recalculate bill totals from items
        $this->recalculateBillTotals($billId);
    }

    /**
     * Update existing bill when prescription updated
     */
    private function updateBill($prescriptionId, $apptId)
    {
        // SQL: SELECT * FROM tbl_bill WHERE appt_id = :appt_id
        $bill = $this->billRepo->findByAppointment($apptId);
        if (!$bill) {
            $this->generateBill($prescriptionId, $apptId);
            return;
        }
        
        // SQL: DELETE FROM tbl_bill_item WHERE bill_id = :bill_id AND item_type = 'medicine'
        $this->billItemRepo->deleteByBillAndType($bill['bill_id'], 'medicine');
        
        // SQL: INSERT INTO tbl_bill_item (...) VALUES (...) (for each medicine)
        $medlines = $this->medlineRepo->findByPrescription($prescriptionId);
        foreach ($medlines as $medline) {
            $med = $this->medicineRepo->findById($medline['med_id']);
            if ($med) {
                $itemTotal = $med['med_price'] * $medline['qty'];
                $this->billItemRepo->create([
                    'bill_id' => $bill['bill_id'],
                    'item_type' => 'medicine',
                    'description' => $med['med_name'] . ' (' . $med['strength'] . ') x' . $medline['qty'],
                    'reference_id' => $med['med_id'],
                    'quantity' => $medline['qty'],
                    'unit_price' => $med['med_price'],
                    'total_price' => $itemTotal,
                ]);
            }
        }
        
        // Recalculate bill totals from items
        $this->recalculateBillTotals($bill['bill_id']);
    }

    /**
     * Recalculate bill totals from bill items
     * SQL: SELECT COALESCE(SUM(total_price), 0) FROM tbl_bill_item WHERE bill_id = :bill_id
     * SQL: UPDATE tbl_bill SET dr_fee = ?, totalm_price = ?, total_amount = ? WHERE bill_id = ?
     */
    private function recalculateBillTotals($billId)
    {
        // SQL: SELECT COALESCE(SUM(total_price), 0) FROM tbl_bill_item WHERE bill_id = :bill_id
        $totalAmount = $this->billItemRepo->getTotalByBill($billId);
        
        // SQL: SELECT COALESCE(SUM(total_price), 0) FROM tbl_bill_item WHERE bill_id = :bill_id AND item_type = 'consultation'
        $consultationTotal = $this->billItemRepo->getTotalByBillAndType($billId, 'consultation');
        
        // SQL: SELECT COALESCE(SUM(total_price), 0) FROM tbl_bill_item WHERE bill_id = :bill_id AND item_type = 'medicine'
        $medicineTotal = $this->billItemRepo->getTotalByBillAndType($billId, 'medicine');
        
        // SQL: UPDATE tbl_bill SET dr_fee = ?, totalm_price = ?, total_amount = ? WHERE bill_id = ?
        $this->billRepo->updateTotals($billId, $consultationTotal, $medicineTotal, $totalAmount);
    }
}