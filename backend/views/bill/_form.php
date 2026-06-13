<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\TblAppointment;
use common\models\TblDoctor;
use common\models\TblPrescription;
use common\models\TblMedline;
use common\models\TblMedicine;
use common\models\TblLabTest;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\TblBill $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="tbl-bill-form">
    <div class="card">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0"><i class="fas fa-file-invoice-dollar"></i> Generate Bill</h4>
        </div>
        <div class="card-body">
            <?php $form = ActiveForm::begin(['options' => ['id' => 'bill-form']]); ?>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'appt_id')->dropDownList(
                        ArrayHelper::map(
                            TblAppointment::find()
                                ->where(['status' => ['in_progress', 'completed', 'checked_in']])
                                ->orderBy(['appt_id' => SORT_ASC])
                                ->all(), 
                            'appt_id', 
                            function($model) { 
                                return $model->appt_id . ' - Date: ' . $model->appointment_date . 
                                       ' | Patient: ' . ($model->patient ? $model->patient->last_name . ', ' . $model->patient->first_name : 'N/A');
                            }
                        ),
                        [
                            'prompt' => '-- Select Appointment --',
                            'id' => 'bill-appt_id',
                            'onchange' => 'loadAppointmentDetails()'
                        ]
                    ) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'payment_status')->dropDownList([ 
                        'pending' => 'Pending', 
                        'partial' => 'Partial', 
                        'paid' => 'Paid', 
                        'refunded' => 'Refunded', 
                        'cancelled' => 'Cancelled', 
                    ], [
                        'prompt' => '-- Select Status --'
                    ]) ?>
                </div>
            </div>

            <?= $form->field($model, 'payment_method')->dropDownList([ 
                'cash' => 'Cash', 
                'credit_card' => 'Credit Card', 
                'debit_card' => 'Debit Card', 
                'insurance' => 'Insurance', 
                'bank_transfer' => 'Bank Transfer',
                'gcash' => 'GCash',
                'maya' => 'Maya',
            ], [
                'prompt' => '-- Select Payment Method --'
            ]) ?>

            <hr>
            <h5><i class="fas fa-calculator"></i> Bill Breakdown</h5>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Doctor's Fee</label>
                        <input type="text" class="form-control" id="bill-dr_fee_display" readonly>
                        <?= $form->field($model, 'dr_fee')->hiddenInput(['id' => 'bill-dr_fee'])->label(false) ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Medicine Total</label>
                        <input type="text" class="form-control" id="bill-medicine_display" readonly>
                        <?= $form->field($model, 'totalm_price')->hiddenInput(['id' => 'bill-totalm_price'])->label(false) ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Lab Tests Total</label>
                        <input type="text" class="form-control" id="bill-labtest_display" readonly>
                    </div>
                </div>
            </div>

            <div class="alert alert-success">
                <h4><strong>Total Amount Due:</strong></h4>
                <h2 id="bill-total_amount_display">₱0.00</h2>
                <?= $form->field($model, 'total_amount')->hiddenInput(['id' => 'bill-total_amount'])->label(false) ?>
            </div>

            <div class="form-group mt-4">
                <?= Html::submitButton('<i class="fas fa-file-invoice"></i> Generate Bill', [
                    'class' => 'btn btn-success btn-lg'
                ]) ?>
                <?= Html::button('<i class="fas fa-sync"></i> Recalculate', [
                    'class' => 'btn btn-info btn-lg',
                    'onclick' => 'loadAppointmentDetails()'
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<?php
$this->registerJs("
    function loadAppointmentDetails() {
        var apptId = $('#bill-appt_id').val();
        if (!apptId) return;
        
        $.ajax({
            url: '" . Url::to(['bill/calculate']) . "',
            type: 'GET',
            data: {appt_id: apptId},
            dataType: 'json',
            beforeSend: function() {
                $('#bill-dr_fee_display').val('Calculating...');
                $('#bill-medicine_display').val('Calculating...');
            },
            success: function(data) {
                if (data.success) {
                    $('#bill-dr_fee').val(data.dr_fee);
                    $('#bill-dr_fee_display').val('₱' + formatNumber(data.dr_fee));
                    
                    $('#bill-totalm_price').val(data.medicine_total);
                    $('#bill-medicine_display').val('₱' + formatNumber(data.medicine_total));
                    
                    $('#bill-labtest_display').val('₱' + formatNumber(data.labtest_total));
                    
                    var grandTotal = parseFloat(data.dr_fee) + parseFloat(data.medicine_total) + parseFloat(data.labtest_total);
                    $('#bill-total_amount').val(grandTotal.toFixed(2));
                    $('#bill-total_amount_display').html('₱' + formatNumber(grandTotal.toFixed(2)));
                }
            }
        });
    }
    
    function formatNumber(num) {
        return parseFloat(num).toFixed(2).replace(/\\d(?=(\\d{3})+\\.)/g, '$&,');
    }
    
    $(document).ready(function() {
        if ($('#bill-appt_id').val()) {
            loadAppointmentDetails();
        }
    });
");
?>