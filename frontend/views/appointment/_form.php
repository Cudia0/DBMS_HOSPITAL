<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\TblDoctor;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var common\models\TblAppointment $model */
/** @var common\models\TblPatient $patient */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="tbl-appointment-form">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="fas fa-calendar-plus"></i> Book an Appointment</h4>
        </div>
        <div class="card-body">
            
            <?php $form = ActiveForm::begin(['options' => ['id' => 'appointment-form']]); ?>

            <div class="alert alert-info">
                <h5 class="mb-2"><i class="fas fa-user"></i> Patient Information</h5>
                <div class="row">
                    <div class="col-md-6"><strong>Name:</strong> <?= Html::encode($patient->getFullName()) ?></div>
                    <div class="col-md-3"><strong>Sex:</strong> <?= Html::encode($patient->sex) ?></div>
                    <div class="col-md-3"><strong>Age:</strong> <?= $patient->getAgeDisplay() ?></div>
                </div>
            </div>

            <hr>
            <h5 class="text-primary mb-3"><i class="fas fa-user-md"></i> Select Doctor</h5>

            <div class="row">
                <div class="col-md-8">
                    <?= $form->field($model, 'dr_id')->dropDownList(
                        ArrayHelper::map(
                            TblDoctor::find()->joinWith('dept')->orderBy(['dr_id' => SORT_ASC])->all(),
                            'dr_id',
                            function($model) {
                                $deptName = $model->dept ? $model->dept->dept_name : 'General';
                                return $model->dr_id . ' - Dr. ' . $model->first_name . ' ' . $model->last_name .
                                       ' | ' . ($model->specialization ?? 'General') .
                                       ' | Dept: ' . $deptName .
                                       ' | Fee: ₱' . number_format($model->dr_fee, 2);
                            }
                        ),
                        ['prompt' => '-- Select Doctor --', 'id' => 'appointment-dr_id', 'class' => 'form-control prompt-select', 'required' => true]
                    )->label('Choose Doctor *') ?>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Consultation Fee</label>
                        <div class="input-group">
                            <span class="input-group-text">₱</span>
                            <input type="text" id="doctor-fee-display" class="form-control" readonly placeholder="Select a doctor" style="font-weight: bold; font-size: 18px;">
                        </div>
                    </div>
                </div>
            </div>

            <hr>
            <h5 class="text-primary mb-3"><i class="fas fa-notes-medical"></i> Reason for Visit</h5>

            <?= $form->field($model, 'symptoms_list')->textarea([
                'rows' => 5,
                'placeholder' => "Please describe your symptoms or reason for consultation...\n\nExample:\n- Fever and cough for 3 days\n- Headache with dizziness\n- Annual checkup",
                'required' => true
            ])->label('Symptoms / Reason for Visit *') ?>

            <div class="alert alert-warning mt-3">
                <i class="fas fa-info-circle"></i> 
                <strong>Note:</strong> Your appointment date and time will be set by the receptionist upon acceptance.
            </div>

            <div class="form-group mt-4">
                <?= Html::submitButton('<i class="fas fa-paper-plane"></i> Submit Appointment Request', ['class' => 'btn btn-primary btn-lg w-100']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.prompt-select').forEach(function(select) {
        var promptOption = select.querySelector('option[value=""]');
        if (promptOption) promptOption.disabled = select.value !== '';
        select.addEventListener('change', function() {
            if (promptOption) promptOption.disabled = this.value !== '';
        });
    });
    
    document.getElementById('appointment-dr_id').addEventListener('change', function() {
        var text = this.options[this.selectedIndex].text;
        var feeMatch = text.match(/Fee: ₱([\d,]+\.\d{2})/);
        var display = document.getElementById('doctor-fee-display');
        if (feeMatch) {
            display.value = feeMatch[1].replace(/,/g, '');
            display.style.backgroundColor = '#d4edda00';
        } else {
            display.value = '';
            display.style.backgroundColor = '#ffffff01';
        }
    });
});
</script>