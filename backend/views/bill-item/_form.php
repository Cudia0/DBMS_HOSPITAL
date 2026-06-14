<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\TblBill;
use common\models\TblBillItem;
use common\models\TblLabTest;
use common\models\TblAppointment;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var common\models\TblBillItem $model */
/** @var common\models\TblBill|null $bill */
/** @var yii\widgets\ActiveForm $form */

$isNewRecord = $model->isNewRecord;
$billId = $model->bill_id;

// Get appointment to find lab tests
$completedLabTests = [];
if ($bill && $bill->appt_id) {
    $completedLabTests = TblLabTest::find()
        ->where(['appt_id' => $bill->appt_id, 'status' => 'completed'])
        ->andWhere(['IS NOT', 'results', null])
        ->all();
}
?>

<div class="tbl-bill-item-form">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">
                <i class="fas fa-list-alt"></i> 
                <?= $isNewRecord ? 'Add Charge to Bill' : 'Update Charge #' . $model->bill_item_id ?>
            </h4>
        </div>
        <div class="card-body">
            
            <?php if ($isNewRecord): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> 
                <strong>What can you add?</strong><br>
                • Lab test fees<br>
                • Medical procedure charges<br>
                • Medical certificate fees<br>
                • Any other hospital charges
            </div>
            <?php endif; ?>

            <?php $form = ActiveForm::begin(['options' => ['id' => 'bill-item-form']]); ?>

            <?php if ($isNewRecord && $billId): ?>
                <?= $form->field($model, 'bill_id')->hiddenInput(['value' => $billId])->label(false) ?>
            <?php else: ?>
                <?= $form->field($model, 'bill_id')->dropDownList(
                    ArrayHelper::map(
                        TblBill::find()->orderBy(['bill_id' => SORT_DESC])->all(),
                        'bill_id',
                        function($model) {
                            return 'Bill #' . $model->bill_id . 
                                   ' | Appt #' . $model->appt_id . 
                                   ' | Total: ₱' . number_format($model->total_amount, 2) . 
                                   ' | ' . ucfirst($model->payment_status);
                        }
                    ),
                    ['prompt' => '-- Select Bill --', 'class' => 'form-control prompt-select', 'required' => true]
                )->label('Bill *') ?>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'item_type')->dropDownList([ 
                        'consultation' => '🩺 Consultation Fee',
                        'medicine' => '💊 Medicine',
                        'lab_test' => '🔬 Laboratory Test',
                        'procedure' => '🏥 Medical Procedure',
                        'other' => '📋 Other Charge',
                    ], [
                        'prompt' => '-- Select Type --',
                        'class' => 'form-control prompt-select',
                        'required' => true
                    ])->label('Item Type *') ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'description')->textInput([
                        'maxlength' => true,
                        'placeholder' => 'e.g., CBC Blood Test, Medical Certificate Fee',
                        'required' => true
                    ])->label('Description *') ?>
                </div>
            </div>

            <?= $form->field($model, 'reference_id')->textInput([
                'type' => 'number',
                'placeholder' => 'Optional - Link to medicine ID, lab test ID, etc.'
            ])->label('Reference ID (Optional)') ?>
            <small class="text-muted">
                <i class="fas fa-info-circle"></i> 
                Links this charge to its source (medicine, lab test, etc.). Auto-filled for prescription items.
            </small>

            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'quantity')->textInput([
                        'type' => 'number',
                        'min' => 1,
                        'value' => $model->quantity ?? 1,
                        'id' => 'item-quantity',
                        'required' => true
                    ])->label('Quantity *') ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'unit_price')->textInput([
                        'type' => 'number',
                        'step' => '0.01',
                        'min' => '0',
                        'id' => 'item-unit_price',
                        'required' => true,
                        'placeholder' => '0.00'
                    ])->label('Unit Price (₱) *') ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'total_price')->textInput([
                        'type' => 'number',
                        'step' => '0.01',
                        'readonly' => true,
                        'id' => 'item-total_price',
                        'style' => 'font-weight: bold; font-size: 18px; background-color: #e8f5e9;'
                    ])->label('Total (Auto)') ?>
                </div>
            </div>

            <!-- Completed Lab Tests - Suggest to add -->
            <?php if (!empty($completedLabTests)): ?>
            <div class="card  mt-3 border-warning">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0"><i class="fas fa-flask"></i> Completed Lab Tests for This Appointment</h6>
                </div>
                <div class="card-body">
                    <p class="small text-muted">These lab tests have been completed. Click to add them as charges:</p>
                    <?php foreach ($completedLabTests as $labTest): ?>
                    <button type="button" class="btn btn-sm btn-outline-warning m-1" 
                            onclick="quickAddLabTest('<?= Html::encode($labTest->test_name) ?>', <?= $labTest->test_id ?>)">
                        🔬 <?= Html::encode($labTest->test_name) ?> 
                        <?= $labTest->results_date ? ' (' . Yii::$app->formatter->asDate($labTest->results_date) . ')' : '' ?>
                    </button>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Common Charges Quick Add -->
            <div class="card  mt-3">
                <div class="card-body">
                    <strong><i class="fas fa-bolt text-warning"></i> Quick Add Common Charges:</strong>
                    <div class="mt-2">
                        <button type="button" class="btn btn-sm btn-outline-primary m-1" onclick="quickAdd('consultation', 'Doctor Consultation Fee', 1, 800)">
                            🩺 Consultation (₱800)
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-info m-1" onclick="quickAdd('lab_test', 'Complete Blood Count (CBC)', 1, 1500)">
                            🔬 CBC Test (₱1,500)
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-info m-1" onclick="quickAdd('lab_test', 'Lipid Profile', 1, 1200)">
                            🔬 Lipid Profile (₱1,200)
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-info m-1" onclick="quickAdd('lab_test', 'Chest X-Ray', 1, 2000)">
                            🔬 X-Ray (₱2,000)
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-info m-1" onclick="quickAdd('lab_test', 'Urinalysis', 1, 500)">
                            🔬 Urinalysis (₱500)
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-warning m-1" onclick="quickAdd('procedure', 'Wound Dressing', 1, 500)">
                            🏥 Wound Dressing (₱500)
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary m-1" onclick="quickAdd('other', 'Medical Certificate', 1, 300)">
                            📋 Med Cert (₱300)
                        </button>
                    </div>
                </div>
            </div>

            <div class="form-group mt-4">
                <?= Html::submitButton('<i class="fas fa-plus-circle"></i> ' . ($isNewRecord ? 'Add Charge to Bill' : 'Update Charge'), [
                    'class' => 'btn btn-success btn-lg'
                ]) ?>
                <?php if ($billId): ?>
                    <?= Html::a('<i class="fas fa-arrow-left"></i> Back to Bill', ['bill/view', 'bill_id' => $billId], ['class' => 'btn btn-secondary btn-lg ms-2']) ?>
                <?php endif; ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<script>
// Real-time calculation
document.getElementById('item-quantity').addEventListener('input', calculateTotal);
document.getElementById('item-unit_price').addEventListener('input', calculateTotal);

function calculateTotal() {
    var qty = parseFloat(document.getElementById('item-quantity').value) || 0;
    var price = parseFloat(document.getElementById('item-unit_price').value) || 0;
    var total = qty * price;
    document.getElementById('item-total_price').value = total.toFixed(2);
    
    var totalField = document.getElementById('item-total_price');
    totalField.style.backgroundColor = total > 0 ? '#e8f5e900' : '#ffffff00';
}

// Quick add common charges
function quickAdd(type, description, qty, price) {
    document.querySelector('select[name*="item_type"]').value = type;
    document.querySelector('input[name*="description"]').value = description;
    document.getElementById('item-quantity').value = qty;
    document.getElementById('item-unit_price').value = price;
    calculateTotal();
}

// Quick add lab test from completed tests
function quickAddLabTest(testName, testId) {
    document.querySelector('select[name*="item_type"]').value = 'lab_test';
    document.querySelector('input[name*="description"]').value = testName;
    document.querySelector('input[name*="reference_id"]').value = testId;
    document.getElementById('item-quantity').value = 1;
    document.getElementById('item-unit_price').value = 1500; // Default lab test price
    calculateTotal();
}

// Dropdown prompt disable
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