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
/** @var common\models\TblBill $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="tbl-bill-form">
    <div class="card">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0"><i class="fas fa-file-invoice-dollar"></i> 
                <?= $model->isNewRecord ? 'Create Bill' : 'Update Bill #' . $model->bill_id ?>
            </h4>
        </div>
        <div class="card-body">
            
            <?php if ($model->isNewRecord): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> 
                <strong>Note:</strong> Bills are usually auto-generated when a doctor creates a prescription.<br>
                You can also manually create a bill here for an appointment.
            </div>
            <?php endif; ?>

            <?php $form = ActiveForm::begin(['options' => ['id' => 'bill-form']]); ?>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'appt_id')->dropDownList(
                        ArrayHelper::map(
                            TblAppointment::find()
                                ->where(['status' => ['checked_in', 'in_progress', 'completed']])
                                ->orderBy(['appt_id' => SORT_DESC])
                                ->all(),
                            'appt_id',
                            function($model) {
                                $patientName = $model->patient ? $model->patient->getFullName() : 'N/A';
                                return '#' . $model->appt_id . ' | ' . $patientName . 
                                       ' | ' . Yii::$app->formatter->asDate($model->appointment_date) .
                                       ' | ' . ucfirst(str_replace('_', ' ', $model->status));
                            }
                        ),
                        [
                            'prompt' => '-- Select Appointment --',
                            'id' => 'bill-appt_id',
                            'class' => 'form-control prompt-select',
                            'required' => true
                        ]
                    )->label('Appointment *') ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'payment_status')->dropDownList([ 
                        'pending' => 'Pending Payment',
                        'partial' => 'Partial Payment',
                        'paid' => 'Paid',
                        'refunded' => 'Refunded',
                        'cancelled' => 'Cancelled',
                    ], [
                        'prompt' => '-- Select Status --',
                        'class' => 'form-control prompt-select'
                    ])->label('Payment Status') ?>
                </div>
            </div>

            <?= $form->field($model, 'payment_method')->dropDownList([ 
                '' => '-- Not yet paid --',
                'cash' => 'Cash',
                'gcash' => 'GCash',
                'maya' => 'Maya',
                'credit_card' => 'Credit Card',
                'debit_card' => 'Debit Card',
                'bank_transfer' => 'Bank Transfer',
                'insurance' => 'Insurance',
            ], [
                'class' => 'form-control'
            ])->label('Payment Method') ?>

            <hr>
            <h5 class="text-primary mb-3"><i class="fas fa-calculator"></i> Bill Amounts</h5>

            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'dr_fee')->textInput([
                        'type' => 'number',
                        'step' => '0.01',
                        'min' => '0',
                        'id' => 'bill-dr_fee',
                        'class' => 'form-control auto-calc',
                        'placeholder' => '0.00'
                    ])->label('Doctor Fee (₱)') ?>
                    <small class="text-muted">Consultation fee charged by the doctor</small>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'totalm_price')->textInput([
                        'type' => 'number',
                        'step' => '0.01',
                        'min' => '0',
                        'id' => 'bill-totalm_price',
                        'class' => 'form-control auto-calc',
                        'placeholder' => '0.00'
                    ])->label('Medicine Total (₱)') ?>
                    <small class="text-muted">Total cost of prescribed medicines</small>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'total_amount')->textInput([
                        'type' => 'number',
                        'step' => '0.01',
                        'readonly' => true,
                        'id' => 'bill-total_amount',
                        'class' => 'form-control',
                        'style' => 'font-weight: bold; font-size: 20px; background-color: #e8f5e9;'
                    ])->label('Grand Total (₱)') ?>
                </div>
            </div>

            <div class="form-group mt-4">
                <?= Html::submitButton('<i class="fas fa-save"></i> ' . ($model->isNewRecord ? 'Create Bill' : 'Update Bill'), [
                    'class' => 'btn btn-success btn-lg'
                ]) ?>
                <?php if (!$model->isNewRecord): ?>
                    <?= Html::a('<i class="fas fa-plus-circle"></i> Add Charge', ['bill-item/create', 'bill_id' => $model->bill_id], ['class' => 'btn btn-info btn-lg ms-2']) ?>
                <?php endif; ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<script>
// Auto-calculate grand total
document.getElementById('bill-dr_fee').addEventListener('input', calculateTotal);
document.getElementById('bill-totalm_price').addEventListener('input', calculateTotal);

function calculateTotal() {
    var drFee = parseFloat(document.getElementById('bill-dr_fee').value) || 0;
    var medTotal = parseFloat(document.getElementById('bill-totalm_price').value) || 0;
    var grandTotal = drFee + medTotal;
    document.getElementById('bill-total_amount').value = grandTotal.toFixed(2);
}

function disablePromptOption(selectElement) {
    var selectedValue = selectElement.value;
    var promptOption = selectElement.querySelector('option[value=""]');
    if (promptOption) {
        promptOption.disabled = selectedValue !== '';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.prompt-select').forEach(function(select) {
        disablePromptOption(select);
        select.addEventListener('change', function() { disablePromptOption(this); });
    });
    calculateTotal();
});
</script>