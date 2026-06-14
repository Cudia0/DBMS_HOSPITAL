<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\TblAppointment;
use common\models\TblMedicine;
use common\models\TblMedline;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var common\models\TblPrescription $model */
/** @var yii\widgets\ActiveForm $form */

$user = Yii::$app->user->identity;
$isDoctor = $user && $user->isDoctor();
$doctorId = $isDoctor ? $user->doctor_id : null;

$appointmentQuery = TblAppointment::find()
    ->where(['status' => ['checked_in', 'in_progress']])
    ->orderBy(['appointment_date' => SORT_DESC, 'appointment_time' => SORT_DESC]);

if ($isDoctor) {
    $appointmentQuery->andWhere(['dr_id' => $doctorId]);
}

$allMedicines = TblMedicine::find()->orderBy(['med_name' => SORT_ASC])->all();

$frequencyOptions = [
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
    'at_bedtime' => 'At Bedtime (HS)',
];

$existingMedlines = [];
if (!$model->isNewRecord) {
    $existingMedlines = TblMedline::find()
        ->where(['prescription_id' => $model->prescription_id])
        ->all();
}
?>

<div class="tbl-prescription-form">
    <div class="card">
        <div class="card-header bg-info text-white">
            <h4 class="mb-0"><i class="fas fa-prescription-bottle-alt"></i> 
                <?= $model->isNewRecord ? 'Create Prescription' : 'Update Prescription #' . $model->prescription_id ?>
            </h4>
        </div>
        <div class="card-body">
            
            <?php if ($isDoctor): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> 
                Creating prescription as <strong><?= Html::encode($user->getFullName()) ?></strong>.
                Only your checked-in patients are shown below.
            </div>
            <?php endif; ?>

            <?php $form = ActiveForm::begin(['options' => ['id' => 'prescription-form']]); ?>

            <div class="row">
                <div class="col-md-12">
                    <?= $form->field($model, 'appt_id')->dropDownList(
                        ArrayHelper::map(
                            $appointmentQuery->all(),
                            'appt_id',
                            function($model) {
                                $patientName = $model->patient ? $model->patient->getFullName() : 'N/A';
                                $doctorName = $model->doctor ? 'Dr. ' . $model->doctor->last_name : 'N/A';
                                $date = $model->appointment_date ? Yii::$app->formatter->asDate($model->appointment_date, 'medium') : '';
                                return '#' . $model->appt_id . ' | ' . $patientName . 
                                       ' | ' . $doctorName . ' | ' . $date .
                                       ' | Status: ' . ucfirst(str_replace('_', ' ', $model->status ?? 'unknown'));
                            }
                        ),
                        [
                            'prompt' => '-- Select Appointment --',
                            'class' => 'form-control prompt-select',
                            'required' => true
                        ]
                    )->label('Appointment *') ?>
                </div>
            </div>

            <hr>
            <h5 class="text-primary mb-3"><i class="fas fa-pills"></i> Prescription Details</h5>

            <?= $form->field($model, 'dosage_instructions')->textarea([
                'rows' => 3,
                'placeholder' => "General instructions for the patient...\n\nExamples:\n- Take all medications with food\n- Complete the full course of antibiotics\n- Avoid alcohol while taking this medication"
            ])->label('General Dosage Instructions') ?>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'duration_days')->textInput([
                        'type' => 'number',
                        'min' => 1,
                        'max' => 365,
                        'placeholder' => 'e.g., 7, 14, 30',
                    ])->label('Duration (Days)') ?>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label">Prescription Date</label>
                        <input type="text" class="form-control" readonly 
                               value="<?= Yii::$app->formatter->asDatetime(date('Y-m-d H:i:s'), 'medium') ?>">
                        <small class="text-muted">Automatically set to current date & time</small>
                    </div>
                </div>
            </div>

            <hr>
            <h5 class="text-primary mb-3"><i class="fas fa-capsules"></i> Medicines</h5>

            <div id="medicines-container">
                <?php if (!empty($existingMedlines)): ?>
                    <?php foreach ($existingMedlines as $index => $medline): ?>
                    <div class="medicine-row row mb-3 p-3  rounded">
                        <div class="col-md-4">
                            <label class="fw-bold">Medicine</label>
                            <select name="medicines[<?= $index ?>][med_id]" class="form-control medicine-select" onchange="calculateTotal()">
                                <option value="">-- Select Medicine --</option>
                                <?php foreach ($allMedicines as $med): ?>
                                    <option value="<?= $med->med_id ?>" data-price="<?= $med->med_price ?>"
                                        <?= $medline->med_id == $med->med_id ? 'selected' : '' ?>>
                                        <?= $med->med_id ?> - <?= $med->med_name ?> (<?= $med->strength ?>) - ₱<?= number_format($med->med_price, 2) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="fw-bold">Quantity</label>
                            <input type="number" name="medicines[<?= $index ?>][qty]" class="form-control qty-input" 
                                   value="<?= $medline->qty ?>" min="1" onchange="calculateTotal()" onkeyup="calculateTotal()" oninput="calculateTotal()">
                        </div>
                        <div class="col-md-2">
                            <label class="fw-bold">Dosage/Intake</label>
                            <input type="text" name="medicines[<?= $index ?>][dosage]" class="form-control" 
                                   value="<?= Html::encode($medline->dosage_per_intake) ?>" 
                                   placeholder="1 tablet, 5mL">
                        </div>
                        <div class="col-md-3">
                            <label class="fw-bold">Frequency</label>
                            <select name="medicines[<?= $index ?>][frequency]" class="form-control">
                                <option value="">-- Select --</option>
                                <?php foreach ($frequencyOptions as $value => $label): ?>
                                    <option value="<?= $value ?>" <?= $medline->frequency == $value ? 'selected' : '' ?>><?= $label ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <label>&nbsp;</label>
                            <button type="button" class="btn btn-danger btn-sm remove-medicine mt-4" onclick="calculateTotal()">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                <div class="medicine-row row mb-3 p-3  rounded">
                    <div class="col-md-4">
                        <label class="fw-bold">Medicine *</label>
                        <select name="medicines[0][med_id]" class="form-control medicine-select" onchange="calculateTotal()">
                            <option value="">-- Select Medicine --</option>
                            <?php foreach ($allMedicines as $med): ?>
                                <option value="<?= $med->med_id ?>" data-price="<?= $med->med_price ?>">
                                    <?= $med->med_id ?> - <?= $med->med_name ?> (<?= $med->strength ?>) - ₱<?= number_format($med->med_price, 2) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="fw-bold">Quantity *</label>
                        <input type="number" name="medicines[0][qty]" class="form-control qty-input" 
                               value="1" min="1" onchange="calculateTotal()" onkeyup="calculateTotal()" oninput="calculateTotal()">
                    </div>
                    <div class="col-md-2">
                        <label class="fw-bold">Dosage/Intake</label>
                        <input type="text" name="medicines[0][dosage]" class="form-control" 
                               placeholder="1 tablet, 5mL">
                    </div>
                    <div class="col-md-3">
                        <label class="fw-bold">Frequency</label>
                        <select name="medicines[0][frequency]" class="form-control">
                            <option value="">-- Select --</option>
                            <?php foreach ($frequencyOptions as $value => $label): ?>
                                <option value="<?= $value ?>"><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label>&nbsp;</label>
                        <button type="button" class="btn btn-danger btn-sm remove-medicine mt-4" onclick="calculateTotal()">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <button type="button" class="btn btn-info mb-3" id="add-medicine">
                <i class="fas fa-plus"></i> Add Another Medicine
            </button>

            <div class="alert alert-success">
                <strong><i class="fas fa-calculator"></i> Total Medicine Cost: </strong>
                <span id="total-medicine-cost" style="font-size: 18px; font-weight: bold;">₱0.00</span>
            </div>

            <?= $form->field($model, 'notes')->textarea([
                'rows' => 2,
                'placeholder' => 'Additional notes for pharmacist or patient...'
            ])->label('Additional Notes') ?>

            <div class="form-group mt-4">
                <?= Html::submitButton('<i class="fas fa-prescription"></i> ' . ($model->isNewRecord ? 'Create Prescription' : 'Update Prescription'), [
                    'class' => 'btn btn-success btn-lg'
                ]) ?>
                <?= Html::a('<i class="fas fa-times"></i> Cancel', ['index'], ['class' => 'btn btn-secondary btn-lg ms-2']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<script>
// Calculate total medicine cost - SIMPLE VERSION
function calculateTotal() {
    var total = 0;
    document.querySelectorAll('.medicine-row').forEach(function(row) {
        var select = row.querySelector('.medicine-select');
        var qtyInput = row.querySelector('.qty-input');
        if (select && qtyInput) {
            var selectedOption = select.options[select.selectedIndex];
            var price = selectedOption ? parseFloat(selectedOption.getAttribute('data-price')) || 0 : 0;
            var qty = parseInt(qtyInput.value) || 0;
            total += price * qty;
        }
    });
    document.getElementById('total-medicine-cost').textContent = '₱' + total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
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
    // Initialize dropdowns
    document.querySelectorAll('.prompt-select').forEach(function(select) {
        disablePromptOption(select);
        select.addEventListener('change', function() {
            disablePromptOption(this);
        });
    });
    
    // Add medicine button
    document.getElementById('add-medicine').addEventListener('click', function() {
        var container = document.getElementById('medicines-container');
        var rowCount = container.querySelectorAll('.medicine-row').length;
        
        var medOptions = '<option value="">-- Select Medicine --</option>';
        <?php foreach ($allMedicines as $med): ?>
        medOptions += '<option value="<?= $med->med_id ?>" data-price="<?= $med->med_price ?>"><?= $med->med_id ?> - <?= Html::encode($med->med_name) ?> (<?= $med->strength ?>) - ₱<?= number_format($med->med_price, 2) ?></option>';
        <?php endforeach; ?>
        
        var freqOptions = '<option value="">-- Select --</option>';
        <?php foreach ($frequencyOptions as $value => $label): ?>
        freqOptions += '<option value="<?= $value ?>"><?= $label ?></option>';
        <?php endforeach; ?>
        
        var html = '<div class="medicine-row row mb-3 p-3  rounded">' +
            '<div class="col-md-4"><label class="fw-bold">Medicine *</label><select name="medicines[' + rowCount + '][med_id]" class="form-control medicine-select" onchange="calculateTotal()">' + medOptions + '</select></div>' +
            '<div class="col-md-2"><label class="fw-bold">Quantity *</label><input type="number" name="medicines[' + rowCount + '][qty]" class="form-control qty-input" value="1" min="1" onchange="calculateTotal()" onkeyup="calculateTotal()" oninput="calculateTotal()"></div>' +
            '<div class="col-md-2"><label class="fw-bold">Dosage/Intake</label><input type="text" name="medicines[' + rowCount + '][dosage]" class="form-control" placeholder="1 tablet, 5mL"></div>' +
            '<div class="col-md-3"><label class="fw-bold">Frequency</label><select name="medicines[' + rowCount + '][frequency]" class="form-control">' + freqOptions + '</select></div>' +
            '<div class="col-md-1"><label>&nbsp;</label><button type="button" class="btn btn-danger btn-sm remove-medicine mt-4" onclick="calculateTotal()"><i class="fas fa-trash"></i></button></div>' +
            '</div>';
        
        container.insertAdjacentHTML('beforeend', html);
    });
    
    // Remove medicine (delegated event)
    document.getElementById('medicines-container').addEventListener('click', function(e) {
        if (e.target.closest('.remove-medicine')) {
            var rows = this.querySelectorAll('.medicine-row');
            if (rows.length > 1) {
                e.target.closest('.medicine-row').remove();
                calculateTotal();
            } else {
                alert('At least one medicine is required.');
            }
        }
    });
    
    // Form validation
    document.getElementById('prescription-form').addEventListener('submit', function(e) {
        var valid = true;
        document.querySelectorAll('.medicine-row').forEach(function(row) {
            var medId = row.querySelector('.medicine-select').value;
            var qty = parseInt(row.querySelector('.qty-input').value) || 0;
            if (!medId || qty < 1) {
                valid = false;
            }
        });
        if (!valid) {
            e.preventDefault();
            alert('Please select a medicine and enter a valid quantity for each row.');
        }
    });
    
    // Initial calculation
    calculateTotal();
});
</script>