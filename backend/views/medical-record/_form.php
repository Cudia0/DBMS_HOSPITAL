<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\TblAppointment;
use common\models\TblPatient;
use common\models\TblDoctor;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var common\models\TblMedicalRecord $model */
/** @var yii\widgets\ActiveForm $form */

$user = Yii::$app->user->identity;
$isDoctor = $user && $user->isDoctor();
$doctorId = $isDoctor ? $user->doctor_id : null;

// Get appointments that are checked_in or in_progress
$appointmentQuery = TblAppointment::find()
    ->where(['status' => ['checked_in', 'in_progress']])
    ->orderBy(['appointment_date' => SORT_DESC, 'appointment_time' => SORT_DESC]);

if ($isDoctor) {
    $appointmentQuery->andWhere(['dr_id' => $doctorId]);
}

// Parse existing vital signs for the form fields
$vitalBp = '';
$vitalHr = '';
$vitalTemp = '';
$vitalRr = '';
$vitalO2 = '';
$vitalWeight = '';
$vitalHeight = '';

if ($model->vital_signs) {
    if (preg_match('/BP:\s*([^,]+)/', $model->vital_signs, $m)) $vitalBp = trim(str_replace('mmHg', '', $m[1]));
    if (preg_match('/HR:\s*([^,]+)/', $model->vital_signs, $m)) $vitalHr = trim(str_replace('bpm', '', $m[1]));
    if (preg_match('/Temp:\s*([^,]+)/', $model->vital_signs, $m)) $vitalTemp = trim(str_replace('°C', '', $m[1]));
    if (preg_match('/RR:\s*([^,]+)/', $model->vital_signs, $m)) $vitalRr = trim(str_replace('/min', '', $m[1]));
    if (preg_match('/O2:\s*([^,]+)/', $model->vital_signs, $m)) $vitalO2 = trim(str_replace('%', '', $m[1]));
    if (preg_match('/Weight:\s*([^,]+)/', $model->vital_signs, $m)) $vitalWeight = trim(str_replace('kg', '', $m[1]));
    if (preg_match('/Height:\s*([^,]+)/', $model->vital_signs, $m)) $vitalHeight = trim(str_replace('cm', '', $m[1]));
}
?>

<div class="tbl-medical-record-form">
    <div class="card">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0"><i class="fas fa-stethoscope"></i> Medical Consultation Record</h4>
        </div>
        <div class="card-body">
            
            <?php if ($isDoctor): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> 
                You are creating a medical record as <strong><?= Html::encode($user->getFullName()) ?></strong>.
                Only your checked-in appointments are shown below.
            </div>
            <?php endif; ?>

            <?php $form = ActiveForm::begin(['options' => ['id' => 'medical-record-form']]); ?>

            <div class="row">
                <div class="col-md-12">
                    <?= $form->field($model, 'appt_id')->dropDownList(
                        ArrayHelper::map(
                            $appointmentQuery->all(),
                            'appt_id',
                            function($model) {
                                $patientName = $model->patient ? $model->patient->getFullName() : 'N/A';
                                $doctorName = $model->doctor ? 'Dr. ' . $model->doctor->last_name : 'N/A';
                                $date = $model->appointment_date ? Yii::$app->formatter->asDate($model->appointment_date, 'medium') : 'No date';
                                $time = $model->appointment_time ? Yii::$app->formatter->asTime($model->appointment_time, 'short') : '';
                                return '#' . $model->appt_id . ' | ' . $patientName . 
                                       ' | ' . $doctorName . ' | ' . $date . ' ' . $time .
                                       ' | Status: ' . ucfirst(str_replace('_', ' ', $model->status));
                            }
                        ),
                        [
                            'prompt' => '-- Select Appointment --',
                            'id' => 'medical-appt_id',
                            'class' => 'form-control prompt-select',
                            'required' => true
                        ]
                    )->label('Appointment *') ?>
                </div>
            </div>

            <hr>
            <h5 class="text-primary mb-3"><i class="fas fa-heartbeat"></i> Vital Signs</h5>

            <div class="row">
                <div class="col-md-3">
                    <div class="form-group mb-3">
                        <label class="form-label">Blood Pressure</label>
                        <div class="input-group">
                            <input type="text" id="vital-bp" class="form-control" placeholder="120/80" 
                                   value="<?= Html::encode($vitalBp) ?>" onchange="updateVitalSigns()" onkeyup="updateVitalSigns()">
                            <span class="input-group-text">mmHg</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mb-3">
                        <label class="form-label">Heart Rate</label>
                        <div class="input-group">
                            <input type="text" id="vital-hr" class="form-control" placeholder="72" 
                                   value="<?= Html::encode($vitalHr) ?>" onchange="updateVitalSigns()" onkeyup="updateVitalSigns()">
                            <span class="input-group-text">bpm</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mb-3">
                        <label class="form-label">Temperature</label>
                        <div class="input-group">
                            <input type="text" id="vital-temp" class="form-control" placeholder="36.8" 
                                   value="<?= Html::encode($vitalTemp) ?>" onchange="updateVitalSigns()" onkeyup="updateVitalSigns()">
                            <span class="input-group-text">°C</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mb-3">
                        <label class="form-label">Respiratory Rate</label>
                        <div class="input-group">
                            <input type="text" id="vital-rr" class="form-control" placeholder="16" 
                                   value="<?= Html::encode($vitalRr) ?>" onchange="updateVitalSigns()" onkeyup="updateVitalSigns()">
                            <span class="input-group-text">/min</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-3">
                    <div class="form-group mb-3">
                        <label class="form-label">O2 Saturation</label>
                        <div class="input-group">
                            <input type="text" id="vital-o2" class="form-control" placeholder="98" 
                                   value="<?= Html::encode($vitalO2) ?>" onchange="updateVitalSigns()" onkeyup="updateVitalSigns()">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mb-3">
                        <label class="form-label">Weight</label>
                        <div class="input-group">
                            <input type="text" id="vital-weight" class="form-control" placeholder="70" 
                                   value="<?= Html::encode($vitalWeight) ?>" onchange="updateVitalSigns()" onkeyup="updateVitalSigns()">
                            <span class="input-group-text">kg</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mb-3">
                        <label class="form-label">Height</label>
                        <div class="input-group">
                            <input type="text" id="vital-height" class="form-control" placeholder="170" 
                                   value="<?= Html::encode($vitalHeight) ?>" onchange="updateVitalSigns()" onkeyup="updateVitalSigns()">
                            <span class="input-group-text">cm</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Combined vital signs hidden field -->
            <?= $form->field($model, 'vital_signs')->hiddenInput([
                'id' => 'vital-signs-hidden',
                'maxlength' => true
            ])->label(false) ?>

            <div class="alert alert-info" id="vital-signs-preview" style="<?= $model->vital_signs ? '' : 'display:none;' ?>">
                <strong><i class="fas fa-heartbeat"></i> Vital Signs Summary:</strong><br>
                <span id="vital-signs-text"><?= Html::encode($model->vital_signs) ?></span>
            </div>

            <hr>
            <h5 class="text-primary mb-3"><i class="fas fa-clipboard-check"></i> Diagnosis & Treatment</h5>

            <?= $form->field($model, 'diagnosis')->textarea([
                'rows' => 4,
                'placeholder' => "Enter diagnosis...\n\nExample:\n- Essential Hypertension Stage 1\n- Upper Respiratory Tract Infection\n- Contact Dermatitis",
                'required' => true
            ])->label('Diagnosis *') ?>

            <?= $form->field($model, 'treatment_plan')->textarea([
                'rows' => 4,
                'placeholder' => "Enter treatment plan...\n\nExample:\n- Prescribed medication (see prescription)\n- Low sodium diet\n- Exercise 30 mins daily\n- Follow-up in 2 weeks",
                'required' => true
            ])->label('Treatment Plan *') ?>

            <?= $form->field($model, 'notes')->textarea([
                'rows' => 3,
                'placeholder' => 'Additional notes, observations, or recommendations...'
            ])->label('Additional Notes') ?>

            <div class="form-group mt-4">
                <?= Html::submitButton('<i class="fas fa-save"></i> Save Medical Record', [
                    'class' => 'btn btn-primary btn-lg'
                ]) ?>
                <?= Html::a('<i class="fas fa-times"></i> Cancel', ['index'], ['class' => 'btn btn-secondary btn-lg ms-2']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<?php
$this->registerJs("
    // Dropdown prompt disable
    function disablePromptOption(selectElement) {
        var selectedValue = $(selectElement).val();
        $(selectElement).find('option[value=\"\"]').prop('disabled', selectedValue !== '');
        if (selectedValue === '') {
            $(selectElement).find('option[value=\"\"]').prop('disabled', false);
        }
    }
    
    $(document).on('change', '.prompt-select', function() {
        disablePromptOption(this);
    });
    
    // Update vital signs combined field
    function updateVitalSigns() {
        var parts = [];
        var bp = $('#vital-bp').val().trim();
        var hr = $('#vital-hr').val().trim();
        var temp = $('#vital-temp').val().trim();
        var rr = $('#vital-rr').val().trim();
        var o2 = $('#vital-o2').val().trim();
        var weight = $('#vital-weight').val().trim();
        var height = $('#vital-height').val().trim();
        
        if (bp) parts.push('BP: ' + bp + ' mmHg');
        if (hr) parts.push('HR: ' + hr + ' bpm');
        if (temp) parts.push('Temp: ' + temp + '°C');
        if (rr) parts.push('RR: ' + rr + '/min');
        if (o2) parts.push('O2: ' + o2 + '%');
        if (weight) parts.push('Weight: ' + weight + ' kg');
        if (height) parts.push('Height: ' + height + ' cm');
        
        var vitalSigns = parts.join(', ');
        $('#vital-signs-hidden').val(vitalSigns);
        
        if (vitalSigns) {
            $('#vital-signs-preview').show();
            $('#vital-signs-text').text(vitalSigns);
        } else {
            $('#vital-signs-preview').hide();
        }
    }
    
    // Form submission - ensure vital signs are combined
    $('#medical-record-form').on('beforeSubmit', function() {
        updateVitalSigns();
        return true;
    });
    
    // Initialize
    $(document).ready(function() {
        $('.prompt-select').each(function() { disablePromptOption(this); });
        
        // Show initial preview if vital signs exist
        if ($('#vital-signs-hidden').val()) {
            $('#vital-signs-preview').show();
        }
    });
");
?>