<?php

namespace frontend\controllers;

use common\models\TblBill;
use common\models\BillSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * BillController implements the CRUD actions for TblBill model.
 */
class BillController extends Controller
{
    public function actionCalculate($appt_id)
{
    \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    
    $appointment = TblAppointment::findOne($appt_id);
    if (!$appointment) {
        return ['success' => false, 'message' => 'Appointment not found'];
    }
    
    $doctor = TblDoctor::findOne($appointment->dr_id);
    $dr_fee = $doctor ? $doctor->dr_fee : 0;
    
    $prescriptions = TblPrescription::find()->where(['appt_id' => $appt_id])->all();
    $medicine_total = 0;
    foreach ($prescriptions as $prescription) {
        $medlines = TblMedline::find()->where(['prescription_id' => $prescription->prescription_id])->all();
        foreach ($medlines as $medline) {
            $medicine = TblMedicine::findOne($medline->med_id);
            if ($medicine) {
                $medicine_total += ($medicine->med_price * $medline->qty);
            }
        }
    }
    
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
     * Lists all TblBill models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new BillSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TblBill model.
     * @param int $bill_id Bill ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($bill_id)
    {
        return $this->render('view', [
            'model' => $this->findModel($bill_id),
        ]);
    }

    /**
     * Creates a new TblBill model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new TblBill();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'bill_id' => $model->bill_id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TblBill model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $bill_id Bill ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($bill_id)
    {
        $model = $this->findModel($bill_id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'bill_id' => $model->bill_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TblBill model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $bill_id Bill ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($bill_id)
    {
        $this->findModel($bill_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the TblBill model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $bill_id Bill ID
     * @return TblBill the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($bill_id)
    {
        if (($model = TblBill::findOne(['bill_id' => $bill_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
