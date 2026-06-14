<?php

namespace backend\controllers;

use common\models\TblBill;
use common\models\TblBillItem;
use common\models\TblAppointment;
use common\models\TblDoctor;
use common\models\TblPrescription;
use common\models\TblMedline;
use common\models\TblMedicine;
use common\models\TblLabTest;
use common\models\BillSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use Yii;

/**
 * BillController - Bills are AUTO-GENERATED when doctor prescribes
 * Receptionist: View bills, Process payments (mark as paid), Print receipts
 * Director: Full access
 * Doctor: View only
 */
class BillController extends Controller
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
                    'actions' => [
                        'delete' => ['POST'],
                        'mark-paid' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Calculate bill totals from appointment
     */
    public function actionCalculate($appt_id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $appointment = TblAppointment::findOne($appt_id);
        if (!$appointment) {
            return ['success' => false];
        }
        
        $doctor = $appointment->doctor;
        $dr_fee = $doctor ? $doctor->dr_fee : 0;
        
        $medicine_total = 0;
        $prescriptions = TblPrescription::find()->where(['appt_id' => $appt_id])->all();
        foreach ($prescriptions as $prescription) {
            $medlines = TblMedline::find()->where(['prescription_id' => $prescription->prescription_id])->all();
            foreach ($medlines as $medline) {
                $medicine = TblMedicine::findOne($medline->med_id);
                if ($medicine) {
                    $medicine_total += ($medicine->med_price * $medline->qty);
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
     * Lists all bills
     */
    public function actionIndex()
    {
        $searchModel = new BillSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->query->orderBy(['bill_date' => SORT_DESC]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single bill with all its items
     */
    public function actionView($bill_id)
    {
        $model = $this->findModel($bill_id);
        
        $billItems = TblBillItem::find()
            ->where(['bill_id' => $bill_id])
            ->orderBy(['item_type' => SORT_ASC])
            ->all();

        return $this->render('view', [
            'model' => $model,
            'billItems' => $billItems,
        ]);
    }

   /**
 * Print bill as PDF receipt
 */
public function actionPrint($bill_id)
{
    $model = $this->findModel($bill_id);
    
    $billItems = TblBillItem::find()
        ->where(['bill_id' => $bill_id])
        ->orderBy(['item_type' => SORT_ASC])
        ->all();
    
    $appointment = TblAppointment::findOne($model->appt_id);
    $patient = $appointment ? $appointment->patient : null;
    $doctor = $appointment ? $appointment->doctor : null;
    
    $prescription = TblPrescription::find()
        ->where(['appt_id' => $model->appt_id])
        ->one();
    
    $labTests = TblLabTest::find()
        ->where(['appt_id' => $model->appt_id])
        ->all();
    
    $content = $this->renderPartial('print', [
        'model' => $model,
        'billItems' => $billItems,
        'patient' => $patient,
        'doctor' => $doctor,
        'appointment' => $appointment,
        'prescription' => $prescription,
        'labTests' => $labTests,
    ]);
    
    // Create writable temp directory
    $tmpDir = Yii::getAlias('@runtime/mpdf');
    if (!is_dir($tmpDir)) {
        mkdir($tmpDir, 0775, true);
    }
    
    $pdf = new \Mpdf\Mpdf([
        'mode' => 'utf-8',
        'format' => 'A4',
        'margin_left' => 10,
        'margin_right' => 10,
        'margin_top' => 10,
        'margin_bottom' => 10,
        'tempDir' => $tmpDir,
    ]);
    
    // Set watermark using mPDF's built-in method
    $pdf->SetWatermarkText('MediSync', 0.04);  // 0.04 = very light (0-1 scale)
    $pdf->showWatermarkText = true;
    $pdf->watermarkTextAlpha = 0.04;            // Alpha transparency
    
    // Use a very light gray color for the watermark
    $pdf->watermark_font = 'Helvetica';
    $pdf->watermarkTextAlpha = 0.04;
    
    $pdf->SetTitle('Receipt - Bill #' . $model->bill_id . ' - MediSync');
    $pdf->SetAuthor('MediSync Hospital');
    $pdf->WriteHTML($content);
    $pdf->Output('Receipt_Bill_' . $model->bill_id . '.pdf', 'I');
    exit;
}

    /**
     * Creates a bill manually (Director only)
     */
    public function actionCreate()
    {
        $model = new TblBill();
        $model->payment_status = 'pending';
        $model->bill_date = date('Y-m-d H:i:s');

        if ($this->request->isPost) {
            if ($model->load($this->request->post())) {
                $model->bill_date = date('Y-m-d H:i:s');
                $model->total_amount = 0;
                
                if ($model->save()) {
                    Yii::$app->session->setFlash('success', '✅ Bill created. Add charges to this bill.');
                    return $this->redirect(['view', 'bill_id' => $model->bill_id]);
                }
            }
        }

        return $this->render('create', ['model' => $model]);
    }

    /**
     * Updates a bill (Director only)
     */
    public function actionUpdate($bill_id)
    {
        $model = $this->findModel($bill_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', '✅ Bill updated.');
            return $this->redirect(['view', 'bill_id' => $model->bill_id]);
        }

        return $this->render('update', ['model' => $model]);
    }

    /**
     * Mark bill as paid (Receptionist)
     */
    public function actionMarkPaid($bill_id)
    {
        $model = $this->findModel($bill_id);
        $user = Yii::$app->user->identity;
        
        if (!$user->isDirector() && !$user->isReceptionist()) {
            Yii::$app->session->setFlash('error', 'You do not have permission to process payments.');
            return $this->redirect(['view', 'bill_id' => $model->bill_id]);
        }
        
        if ($model->payment_status === 'pending' || $model->payment_status === 'partial') {
            if (Yii::$app->request->isPost) {
                $model->payment_status = 'paid';
                $model->payment_method = Yii::$app->request->post('payment_method', $model->payment_method);
                
                if ($model->save()) {
                    $appointment = TblAppointment::findOne($model->appt_id);
                    if ($appointment && $appointment->status === 'in_progress') {
                        $appointment->status = 'completed';
                        $appointment->save();
                    }
                    
                    Yii::$app->session->setFlash('success', 
                        '✅ Payment processed!<br>' .
                        'Amount: <strong>₱' . number_format($model->total_amount, 2) . '</strong><br>' .
                        'Method: <strong>' . ucfirst($model->payment_method) . '</strong>'
                    );
                }
            }
        } else {
            Yii::$app->session->setFlash('warning', 'This bill is already marked as ' . $model->payment_status . '.');
        }
        
        return $this->redirect(['view', 'bill_id' => $model->bill_id]);
    }

    /**
     * Deletes a bill (Director only)
     */
    public function actionDelete($bill_id)
    {
        if (!Yii::$app->user->identity->isDirector()) {
            Yii::$app->session->setFlash('error', 'Only Director can delete bills.');
            return $this->redirect(['index']);
        }
        $this->findModel($bill_id)->delete();
        return $this->redirect(['index']);
    }

    protected function findModel($bill_id)
    {
        if (($model = TblBill::findOne(['bill_id' => $bill_id])) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}