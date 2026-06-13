<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\TblPrescription;
use common\models\TblMedicine;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var app\models\TblMedline $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="tbl-medline-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'prescription_id')->dropDownList(
        ArrayHelper::map(
            TblPrescription::find()
                ->orderBy(['prescription_id' => SORT_ASC])
                ->all(), 
            'prescription_id', 
            function($model) { 
                return $model->prescription_id . ' - Rx Date: ' . Yii::$app->formatter->asDate($model->prescription_date) . ' | Appt ID: ' . $model->appt_id; 
            }
        ),
        ['prompt' => 'Select Prescription']
    ) ?>

    <?= $form->field($model, 'med_id')->dropDownList(
        ArrayHelper::map(
            TblMedicine::find()
                ->orderBy(['med_id' => SORT_ASC])
                ->all(), 
            'med_id', 
            function($model) { 
                return $model->med_id . ' - ' . $model->med_name . ' ' . ($model->strength ? '(' . $model->strength . ')' : '') . ' - $' . number_format($model->med_price, 2) . ' [' . ($model->dosage_form ?? 'N/A') . ']'; 
            }
        ),
        [
            'prompt' => 'Select Medicine',
            'id' => 'medline-med_id',
            'onchange' => 'updatePrice()'
        ]
    ) ?>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'qty')->textInput([
                'type' => 'number', 
                'min' => 1,
                'id' => 'medline-qty'
            ]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'dosage_per_intake')->textInput([
                'maxlength' => true, 
                'placeholder' => '1 tablet, 5mL',
                'id' => 'medline-dosage'
            ]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'frequency')->dropDownList([ 
                'once_daily' => 'Once Daily (OD)',
                'twice_daily' => 'Twice Daily (BID)', 
                'three_times_daily' => 'Three Times Daily (TID)', 
                'four_times_daily' => 'Four Times Daily (QID)', 
                'every_4_hours' => 'Every 4 Hours (Q4H)', 
                'every_6_hours' => 'Every 6 Hours (Q6H)', 
                'every_8_hours' => 'Every 8 Hours (Q8H)', 
                'every_12_hours' => 'Every 12 Hours (Q12H)', 
                'once_weekly' => 'Once Weekly',
                'as_needed' => 'As Needed (PRN)',
                'before_meals' => 'Before Meals (AC)',
                'after_meals' => 'After Meals (PC)',
            ], ['prompt' => 'Select Frequency']) ?>
        </div>
    </div>

    <div class="alert alert-info" id="medicine-info" style="display:none;">
        <strong>Selected Medicine:</strong> <span id="medicine-name"></span><br>
        <strong>Price per unit:</strong> $<span id="medicine-price"></span><br>
        <strong>Total Cost:</strong> $<span id="medicine-total"></span>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Add to Prescription', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$this->registerJs("
    var medicinePrices = {};
    
    // Load medicine prices
    $(document).ready(function() {
        $.ajax({
            url: '" . \yii\helpers\Url::to(['medicine/get-prices']) . "',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                medicinePrices = response;
            }
        });
    });
    
    function updatePrice() {
        var medId = $('#medline-med_id').val();
        var qty = $('#medline-qty').val() || 0;
        
        if (medId && medicinePrices[medId]) {
            var price = medicinePrices[medId].price;
            var name = medicinePrices[medId].name;
            var total = (price * qty).toFixed(2);
            
            $('#medicine-name').text(name);
            $('#medicine-price').text(parseFloat(price).toFixed(2));
            $('#medicine-total').text(total);
            $('#medicine-info').show();
        } else {
            $('#medicine-info').hide();
        }
    }
    
    $('#medline-qty').on('keyup change', function() {
        updatePrice();
    });
");
?>