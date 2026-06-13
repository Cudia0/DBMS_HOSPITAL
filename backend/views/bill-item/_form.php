<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\TblBill;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var app\models\TblBillItem $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="tbl-bill-item-form">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="fas fa-list-alt"></i> Add Bill Item</h4>
        </div>
        <div class="card-body">
            <?php $form = ActiveForm::begin(['options' => ['id' => 'bill-item-form']]); ?>

            <?= $form->field($model, 'bill_id')->dropDownList(
                ArrayHelper::map(
                    TblBill::find()
                        ->orderBy(['bill_id' => SORT_ASC])
                        ->all(), 
                    'bill_id', 
                    function($model) { 
                        return $model->bill_id . ' - Appt: ' . ($model->appt_id ?? 'N/A') . 
                               ' | Total: ₱' . number_format($model->total_amount, 2) . 
                               ' | Status: ' . $model->payment_status;
                    }
                ),
                ['prompt' => '-- Select Bill --']
            ) ?>

            <?= $form->field($model, 'item_type')->dropDownList([ 
                'consultation' => 'Consultation', 
                'medicine' => 'Medicine', 
                'lab_test' => 'Lab Test', 
                'procedure' => 'Procedure', 
                'other' => 'Other', 
            ], ['prompt' => '-- Select Item Type --']) ?>

            <?= $form->field($model, 'description')->textInput([
                'maxlength' => true,
                'placeholder' => 'Item description...'
            ]) ?>

            <?= $form->field($model, 'reference_id')->textInput([
                'type' => 'number',
                'placeholder' => 'Reference ID (optional)'
            ]) ?>

            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'quantity')->textInput([
                        'type' => 'number',
                        'min' => 1,
                        'class' => 'form-control auto-calc',
                        'id' => 'item-quantity',
                        'onchange' => 'calculateItemTotal()',
                        'onkeyup' => 'calculateItemTotal()'
                    ]) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'unit_price')->textInput([
                        'type' => 'number',
                        'step' => '0.01',
                        'min' => 0,
                        'class' => 'form-control auto-calc',
                        'id' => 'item-unit_price',
                        'onchange' => 'calculateItemTotal()',
                        'onkeyup' => 'calculateItemTotal()'
                    ]) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'total_price')->textInput([
                        'type' => 'number',
                        'step' => '0.01',
                        'min' => 0,
                        'readonly' => true,
                        'class' => 'form-control',
                        'id' => 'item-total_price'
                    ]) ?>
                </div>
            </div>

            <!-- Quick Add Buttons -->
            <div class="alert alert-info">
                <strong><i class="fas fa-bolt"></i> Quick Add:</strong>
                <button type="button" class="btn btn-sm btn-outline-info" onclick="setConsultationFee()">
                    <i class="fas fa-stethoscope"></i> Consultation Fee (₱500)
                </button>
                <button type="button" class="btn btn-sm btn-outline-info" onclick="setLabTest()">
                    <i class="fas fa-flask"></i> Lab Test (₱1,500)
                </button>
            </div>

            <div class="form-group mt-4">
                <?= Html::submitButton('<i class="fas fa-plus-circle"></i> Add Item', [
                    'class' => 'btn btn-primary btn-lg'
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<?php
$this->registerJs("
    function calculateItemTotal() {
        var qty = parseFloat($('#item-quantity').val()) || 0;
        var unitPrice = parseFloat($('#item-unit_price').val()) || 0;
        var total = qty * unitPrice;
        $('#item-total_price').val(total.toFixed(2));
        
        if (total > 0) {
            $('#item-total_price').css('background-color', '#d4edda');
        } else {
            $('#item-total_price').css('background-color', '#ffffff');
        }
    }
    
    function setConsultationFee() {
        $('#item-quantity').val(1);
        $('#item-unit_price').val(500.00);
        calculateItemTotal();
        $('select[name*=\"item_type\"]').val('consultation');
        $('input[name*=\"description\"]').val('Doctor Consultation Fee');
    }
    
    function setLabTest() {
        $('#item-quantity').val(1);
        $('#item-unit_price').val(1500.00);
        calculateItemTotal();
        $('select[name*=\"item_type\"]').val('lab_test');
        $('input[name*=\"description\"]').val('Standard Laboratory Test');
    }
    
    $(document).ready(function() {
        calculateItemTotal();
        
        var calcInterval;
        $('.auto-calc').on('focus', function() {
            calcInterval = setInterval(calculateItemTotal, 100);
        }).on('blur', function() {
            clearInterval(calcInterval);
            calculateItemTotal();
        });
    });
");
?>