<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\TblAppointment;
use common\models\TblMedicine;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var app\models\TblPrescription $model */
/** @var yii\widgets\ActiveForm $form */

$allMedicines = TblMedicine::find()->orderBy(['med_name' => SORT_ASC])->all();
$medicineOptions = [];
foreach ($allMedicines as $med) {
    $medicineOptions[] = [
        'id' => $med->med_id,
        'name' => $med->med_name,
        'strength' => $med->strength,
        'price' => $med->med_price,
        'label' => $med->med_id . ' - ' . $med->med_name . ' (' . $med->strength . ') - ₱' . number_format($med->med_price, 2),
    ];
}
$medicineOptionsJson = json_encode($medicineOptions);
?>

<div class="tbl-prescription-form">
    <div class="card">
        <div class="card-header bg-info text-white">
            <h4 class="mb-0"><i class="fas fa-prescription-bottle-alt"></i> Create Prescription</h4>
        </div>
        <div class="card-body">
            <?php $form = ActiveForm::begin(['options' => ['id' => 'prescription-form']]); ?>

            <?= $form->field($model, 'appt_id')->dropDownList(
                ArrayHelper::map(
                    TblAppointment::find()
                        ->where(['status' => ['in_progress', 'checked_in']])
                        ->orderBy(['appt_id' => SORT_ASC])
                        ->all(), 
                    'appt_id', 
                    function($model) { 
                        $patientName = $model->patient ? $model->patient->last_name . ', ' . $model->patient->first_name : 'N/A';
                        $doctorName = $model->doctor ? 'Dr. ' . $model->doctor->last_name : 'N/A';
                        return $model->appt_id . ' | Date: ' . $model->appointment_date . 
                               ' | Patient: ' . $patientName .
                               ' | Doctor: ' . $doctorName;
                    }
                ),
                ['prompt' => '-- Select Appointment --']
            )->label('Appointment *') ?>

            <?= $form->field($model, 'dosage_instructions')->textarea([
                'rows' => 3,
                'placeholder' => 'General dosage instructions...'
            ]) ?>

            <?= $form->field($model, 'duration_days')->textInput([
                'type' => 'number',
                'min' => 1,
                'placeholder' => '7'
            ]) ?>

            <?= $form->field($model, 'notes')->textarea([
                'rows' => 2,
                'placeholder' => 'Additional notes...'
            ]) ?>

            <hr>
            <h5 class="text-info"><i class="fas fa-pills"></i> Medicines</h5>
            
            <div id="medicines-container">
                <div class="medicine-row row mb-3">
                    <div class="col-md-4">
                        <label>Medicine</label>
                        <select name="medicines[0][med_id]" class="form-control medicine-select" onchange="calculateMedicineTotal()">
                            <option value="">-- Select Medicine --</option>
                            <?php foreach ($allMedicines as $med): ?>
                                <option value="<?= $med->med_id ?>" data-price="<?= $med->med_price ?>">
                                    <?= $med->med_id ?> - <?= $med->med_name ?> (<?= $med->strength ?>) - ₱<?= number_format($med->med_price, 2) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>Quantity</label>
                        <input type="number" name="medicines[0][qty]" class="form-control qty-input" 
                               value="1" min="1" onchange="calculateMedicineTotal()">
                    </div>
                    <div class="col-md-2">
                        <label>Dosage</label>
                        <input type="text" name="medicines[0][dosage]" class="form-control" placeholder="1 tablet">
                    </div>
                    <div class="col-md-3">
                        <label>Frequency</label>
                        <select name="medicines[0][frequency]" class="form-control">
                            <option value="once_daily">Once Daily</option>
                            <option value="twice_daily">Twice Daily</option>
                            <option value="three_times_daily">Three Times Daily</option>
                            <option value="four_times_daily">Four Times Daily</option>
                            <option value="as_needed">As Needed (PRN)</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label>&nbsp;</label>
                        <button type="button" class="btn btn-danger btn-sm remove-medicine">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>

            <button type="button" class="btn btn-info" id="add-medicine">
                <i class="fas fa-plus"></i> Add Medicine
            </button>

            <div class="alert alert-info mt-3">
                <strong><i class="fas fa-calculator"></i> Total Medicine Cost: </strong>
                <span id="total-medicine-cost">₱0.00</span>
            </div>

            <div class="form-group mt-4">
                <?= Html::submitButton('<i class="fas fa-prescription"></i> Save Prescription', [
                    'class' => 'btn btn-success btn-lg'
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<?php
$this->registerJs("
    var medicineOptions = " . $medicineOptionsJson . ";
    var medicineIndex = 1;
    
    $('#add-medicine').click(function() {
        var optionsHtml = '<option value=\"\">-- Select Medicine --</option>';
        for (var i = 0; i < medicineOptions.length; i++) {
            var med = medicineOptions[i];
            optionsHtml += '<option value=\"' + med.id + '\" data-price=\"' + med.price + '\">' + med.label + '</option>';
        }
        
        var template = 
            '<div class=\"medicine-row row mb-3\">' +
            '<div class=\"col-md-4\"><select name=\"medicines[' + medicineIndex + '][med_id]\" class=\"form-control medicine-select\" onchange=\"calculateMedicineTotal()\">' + optionsHtml + '</select></div>' +
            '<div class=\"col-md-2\"><input type=\"number\" name=\"medicines[' + medicineIndex + '][qty]\" class=\"form-control qty-input\" value=\"1\" min=\"1\" onchange=\"calculateMedicineTotal()\"></div>' +
            '<div class=\"col-md-2\"><input type=\"text\" name=\"medicines[' + medicineIndex + '][dosage]\" class=\"form-control\" placeholder=\"1 tablet\"></div>' +
            '<div class=\"col-md-3\"><select name=\"medicines[' + medicineIndex + '][frequency]\" class=\"form-control\"><option value=\"once_daily\">Once Daily</option><option value=\"twice_daily\">Twice Daily</option><option value=\"three_times_daily\">Three Times Daily</option><option value=\"four_times_daily\">Four Times Daily</option><option value=\"as_needed\">As Needed (PRN)</option></select></div>' +
            '<div class=\"col-md-1\"><button type=\"button\" class=\"btn btn-danger btn-sm remove-medicine\"><i class=\"fas fa-trash\"></i></button></div>' +
            '</div>';
        
        $('#medicines-container').append(template);
        medicineIndex++;
    });
    
    $(document).on('click', '.remove-medicine', function() {
        $(this).closest('.medicine-row').remove();
        calculateMedicineTotal();
    });
    
    function calculateMedicineTotal() {
        var total = 0;
        $('.medicine-row').each(function() {
            var select = $(this).find('.medicine-select');
            var qty = $(this).find('.qty-input').val() || 0;
            var price = select.find('option:selected').data('price') || 0;
            total += (parseFloat(price) * parseInt(qty));
        });
        $('#total-medicine-cost').text('₱' + total.toFixed(2).replace(/\\B(?=(\\d{3})+(?!\\d))/g, ','));
    }
    
    calculateMedicineTotal();
");
?>