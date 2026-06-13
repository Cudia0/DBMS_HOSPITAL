<?php

namespace backend\controllers;

use common\models\TblBill;
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
 * BillController - Director & Receptionist can manage, Doctor can view
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
                            'roles' => ['@'],
                            'matchCallback' => function ($rule, $action) {
                                $user = Yii::$app->user->identity;
                                if (in_array($action->id, ['index', 'view'])) {
                                    return $user->isDirector() || $user->isReceptionist() || $user->isDoctor();
                                }
                                return $user->isDirector() || $user->isReceptionist();
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
     * Calculate bill totals from appointment
     */
    public function actionCalculate($appt_id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        $appointment = TblAppointment::findOne($appt_id);
        if (!$appointment) {
            return ['success' => false, 'message' => 'Appointment not found'];
        }
        
        // Get doctor fee from the doctor assigned to this appointment
        $doctor = $appointment->doctor;
        $dr_fee = $doctor ? $doctor->dr_fee : 0;
        
        // Calculate medicine total from prescriptions linked to this appointment
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
        
        // Calculate lab test total
        $labTests = TblLabTest::find()->where(['appt_id' => $appt_id, 'status' => 'completed'])->all();
        $labtest_total = count($labTests) * 1500;
        
        return [
            'success' => true,
            'dr_fee' => $dr_fee,
            'medicine_total' => $medicine_total,
            'labtest_total' => $labtest_total,
            'grand_total' => $dr_fee + $medicine_total + $labtest_total
        ];
    }

    public function actionIndex()
    {
        $searchModel = new BillSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        return $this->render('index', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]);
    }

    public function actionView($bill_id)
    {
        return $this->render('view', ['model' => $this->findModel($bill_id)]);
    }

    public function actionCreate()
    {
        $model = new TblBill();
        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Bill created successfully.');
            return $this->redirect(['view', 'bill_id' => $model->bill_id]);
        }
        return $this->render('create', ['model' => $model]);
    }

    public function actionUpdate($bill_id)
    {
        $model = $this->findModel($bill_id);
        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Bill updated successfully.');
            return $this->redirect(['view', 'bill_id' => $model->bill_id]);
        }
        return $this->render('update', ['model' => $model]);
    }

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