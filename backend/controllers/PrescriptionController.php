<?php

namespace backend\controllers;

use common\models\TblPrescription;
use common\models\TblMedline;
use common\models\TblAppointment;
use common\models\TblBill;
use common\models\TblBillItem;
use common\models\TblMedicine;
use common\models\PrescriptionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use Yii;

/**
 * PrescriptionController - Director & Doctor manage, Receptionist can view
 */
class PrescriptionController extends Controller
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
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists prescriptions - Filtered for doctor to show only their patients
     */
    public function actionIndex()
    {
        $searchModel = new PrescriptionSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        
        $user = Yii::$app->user->identity;
        
        if ($user->isDoctor()) {
            $dataProvider->query->joinWith('appointment')
                ->andWhere(['tbl_appointment.dr_id' => $user->doctor_id]);
        }
        
        $dataProvider->query->orderBy(['prescription_date' => SORT_DESC]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single prescription
     */
    public function actionView($prescription_id)
    {
        $model = $this->findModel($prescription_id);
        
        $user = Yii::$app->user->identity;
        if ($user->isDoctor() && $model->appointment && $model->appointment->dr_id !== $user->doctor_id) {
            throw new \yii\web\ForbiddenHttpException('You can only view prescriptions for your own patients.');
        }
        
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new prescription + Auto-generates bill
     */
    public function actionCreate($appt_id = null)
    {
        $model = new TblPrescription();
        
        if ($appt_id) {
            $existingPrescription = TblPrescription::find()
                ->where(['appt_id' => $appt_id])
                ->one();
            
            if ($existingPrescription) {
                Yii::$app->session->setFlash('warning', 
                    '⚠️ A prescription already exists for this appointment (Prescription #' . $existingPrescription->prescription_id . '). ' .
                    'You can update the existing prescription instead.'
                );
                return $this->redirect(['update', 'prescription_id' => $existingPrescription->prescription_id]);
            }
            
            $model->appt_id = $appt_id;
        }

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                $existingPrescription = TblPrescription::find()
                    ->where(['appt_id' => $model->appt_id])
                    ->andFilterWhere(['!=', 'prescription_id', $model->prescription_id])
                    ->one();
                
                if ($existingPrescription) {
                    Yii::$app->session->setFlash('error', 
                        '❌ A prescription already exists for this appointment. Please update the existing one.'
                    );
                    return $this->redirect(['update', 'prescription_id' => $existingPrescription->prescription_id]);
                }
                
                $model->prescription_date = date('Y-m-d H:i:s');
                
                $transaction = Yii::$app->db->beginTransaction();
                try {
                    if ($model->save()) {
                        // Save medicines (Medline)
                        $medicines = Yii::$app->request->post('medicines', []);
                        $totalMedicinePrice = 0;
                        
                        foreach ($medicines as $medicine) {
                            if (!empty($medicine['med_id']) && !empty($medicine['qty']) && $medicine['qty'] > 0) {
                                $medline = new TblMedline();
                                $medline->prescription_id = $model->prescription_id;
                                $medline->med_id = $medicine['med_id'];
                                $medline->qty = $medicine['qty'];
                                $medline->dosage_per_intake = $medicine['dosage'] ?? null;
                                $medline->frequency = $medicine['frequency'] ?? null;
                                $medline->save();
                                
                                $med = TblMedicine::findOne($medicine['med_id']);
                                if ($med) {
                                    $totalMedicinePrice += ($med->med_price * $medicine['qty']);
                                }
                            }
                        }
                        
                        // AUTO-GENERATE BILL
                        $this->generateBill($model);
                        
                        $transaction->commit();
                        Yii::$app->session->setFlash('success', 
                            '✅ Prescription created successfully!<br>A bill has been automatically generated for this appointment.'
                        );
                        return $this->redirect(['view', 'prescription_id' => $model->prescription_id]);
                    }
                    $transaction->rollBack();
                } catch (\Exception $e) {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('error', '❌ Error: ' . $e->getMessage());
                }
            }
        }

        return $this->render('create', ['model' => $model]);
    }

    /**
     * Updates an existing prescription + Updates the bill
     */
    public function actionUpdate($prescription_id)
    {
        $model = $this->findModel($prescription_id);

        if ($this->request->isPost && $model->load($this->request->post())) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                if ($model->save()) {
                    // Delete old medline records
                    TblMedline::deleteAll(['prescription_id' => $model->prescription_id]);
                    
                    $medicines = Yii::$app->request->post('medicines', []);
                    
                    foreach ($medicines as $medicine) {
                        if (!empty($medicine['med_id']) && !empty($medicine['qty']) && $medicine['qty'] > 0) {
                            $medline = new TblMedline();
                            $medline->prescription_id = $model->prescription_id;
                            $medline->med_id = $medicine['med_id'];
                            $medline->qty = $medicine['qty'];
                            $medline->dosage_per_intake = $medicine['dosage'] ?? null;
                            $medline->frequency = $medicine['frequency'] ?? null;
                            $medline->save();
                        }
                    }
                    
                    // UPDATE EXISTING BILL
                    $this->updateBill($model);
                    
                    $transaction->commit();
                    Yii::$app->session->setFlash('success', '✅ Prescription updated successfully! Bill has been updated.');
                    return $this->redirect(['view', 'prescription_id' => $model->prescription_id]);
                }
                $transaction->rollBack();
            } catch (\Exception $e) {
                $transaction->rollBack();
                Yii::$app->session->setFlash('error', '❌ Error: ' . $e->getMessage());
            }
        }

        return $this->render('update', ['model' => $model]);
    }

    /**
     * Deletes a prescription (Director only)
     */
    public function actionDelete($prescription_id)
    {
        if (!Yii::$app->user->identity->isDirector()) {
            Yii::$app->session->setFlash('error', 'Only Director can delete prescriptions.');
            return $this->redirect(['index']);
        }
        $this->findModel($prescription_id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * Auto-generate bill from prescription
     * Bill total = Sum of ALL bill items (single source of truth)
     */
    private function generateBill($prescription)
    {
        $appointment = TblAppointment::findOne($prescription->appt_id);
        if (!$appointment) return;
        
        // Check if bill already exists for this appointment
        $existingBill = TblBill::find()->where(['appt_id' => $prescription->appt_id])->one();
        if ($existingBill) {
            $this->updateBill($prescription);
            return;
        }
        
        $doctor = $appointment->doctor;
        $drFee = $doctor ? $doctor->dr_fee : 0;
        
        // Create Bill with 0 totals (calculated from bill items)
        $bill = new TblBill();
        $bill->appt_id = $prescription->appt_id;
        $bill->dr_fee = 0;
        $bill->totalm_price = 0;
        $bill->total_amount = 0;
        $bill->payment_status = 'pending';
        $bill->bill_date = date('Y-m-d H:i:s');
        $bill->save();
        
        // Create Consultation Bill Item
        if ($drFee > 0) {
            $consultationItem = new TblBillItem();
            $consultationItem->bill_id = $bill->bill_id;
            $consultationItem->item_type = 'consultation';
            $consultationItem->description = 'Doctor Consultation Fee - Dr. ' . ($doctor ? $doctor->last_name : 'N/A');
            $consultationItem->quantity = 1;
            $consultationItem->unit_price = $drFee;
            $consultationItem->total_price = $drFee;
            $consultationItem->save();
        }
        
        // Create Medicine Bill Items
        $medlines = TblMedline::find()->where(['prescription_id' => $prescription->prescription_id])->all();
        foreach ($medlines as $medline) {
            $medicine = TblMedicine::findOne($medline->med_id);
            if ($medicine) {
                $itemTotal = $medicine->med_price * $medline->qty;
                $billItem = new TblBillItem();
                $billItem->bill_id = $bill->bill_id;
                $billItem->item_type = 'medicine';
                $billItem->description = $medicine->med_name . ' (' . $medicine->strength . ') x' . $medline->qty;
                $billItem->reference_id = $medicine->med_id;
                $billItem->quantity = $medline->qty;
                $billItem->unit_price = $medicine->med_price;
                $billItem->total_price = $itemTotal;
                $billItem->save();
            }
        }
        
        // Recalculate bill totals FROM BILL ITEMS
        $this->recalculateBillTotals($bill->bill_id);
    }

    /**
     * Update existing bill when prescription is updated
     */
    private function updateBill($prescription)
    {
        $appointment = TblAppointment::findOne($prescription->appt_id);
        if (!$appointment) return;
        
        $bill = TblBill::find()->where(['appt_id' => $prescription->appt_id])->one();
        if (!$bill) {
            $this->generateBill($prescription);
            return;
        }
        
        // Keep consultation item, delete only medicine items
        TblBillItem::deleteAll(['bill_id' => $bill->bill_id, 'item_type' => 'medicine']);
        
        // Recreate medicine items
        $medlines = TblMedline::find()->where(['prescription_id' => $prescription->prescription_id])->all();
        foreach ($medlines as $medline) {
            $medicine = TblMedicine::findOne($medline->med_id);
            if ($medicine) {
                $itemTotal = $medicine->med_price * $medline->qty;
                $billItem = new TblBillItem();
                $billItem->bill_id = $bill->bill_id;
                $billItem->item_type = 'medicine';
                $billItem->description = $medicine->med_name . ' (' . $medicine->strength . ') x' . $medline->qty;
                $billItem->reference_id = $medicine->med_id;
                $billItem->quantity = $medline->qty;
                $billItem->unit_price = $medicine->med_price;
                $billItem->total_price = $itemTotal;
                $billItem->save();
            }
        }
        
        // Recalculate bill totals FROM BILL ITEMS
        $this->recalculateBillTotals($bill->bill_id);
    }

    /**
     * Recalculate bill totals from bill items (SINGLE SOURCE OF TRUTH)
     */
    private function recalculateBillTotals($bill_id)
    {
        $bill = TblBill::findOne($bill_id);
        if (!$bill) return;
        
        // Sum ALL bill items for this bill
        $totalAmount = TblBillItem::find()
            ->where(['bill_id' => $bill_id])
            ->sum('total_price') ?? 0;
        
        // Sum consultation items
        $consultationTotal = TblBillItem::find()
            ->where(['bill_id' => $bill_id, 'item_type' => 'consultation'])
            ->sum('total_price') ?? 0;
        
        // Sum medicine items
        $medicineTotal = TblBillItem::find()
            ->where(['bill_id' => $bill_id, 'item_type' => 'medicine'])
            ->sum('total_price') ?? 0;
        
        // Update bill
        $bill->dr_fee = $consultationTotal;
        $bill->totalm_price = $medicineTotal;
        $bill->total_amount = $totalAmount;
        $bill->save();
    }

    /**
     * Finds the TblPrescription model
     */
    protected function findModel($prescription_id)
    {
        if (($model = TblPrescription::findOne(['prescription_id' => $prescription_id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}