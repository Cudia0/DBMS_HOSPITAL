<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\TblDoctor;
use common\models\TblPatient;
use common\models\TblReceptionist;
use common\models\TblDepartment;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var common\models\TblAppointment $model */
/** @var yii\widgets\ActiveForm $form */

// Get current user role
$user = Yii::$app->user->identity;
$isDirector = $user && $user->isDirector();
$isReceptionist = $user && $user->isReceptionist();
?>

<div class="tbl-appointment-form">
    <div class="card">
        <div class="card-header bg-info text-white">
            <h4 class="mb-0">
                <i class="fas fa-calendar-alt"></i> 
                <?= $model->isNewRecord ? 'Create Appointment' : 'Update Appointment #' . $model->appt_id ?>
            </h4>
        </div>
        <div class="card-body">
            
            <?php if ($model->isNewRecord): ?>
            <div class="alert alert-info">
                <strong><i class="fas fa-info-circle"></i> Note:</strong> 
                Creating an appointment on behalf of a patient. The appointment will be set as <strong>checked_in</strong> by default.
            </div>
            <?php endif; ?>

            <?php $form = ActiveForm::begin(['options' => ['id' => 'appointment-form']]); ?>

            <!-- PATIENT & DOCTOR SELECTION -->
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'patient_id')->dropDownList(
                        ArrayHelper::map(
                            TblPatient::find()->orderBy(['patient_id' => SORT_ASC])->all(),
                            'patient_id',
                            function($model) {
                                return $model->patient_id . ' - ' . $model->last_name . ', ' . $model->first_name . ' (' . $model->sex . ')';
                            }
                        ),
                        [
                            'prompt' => '-- Select Patient --',
                            'id' => 'appointment-patient_id',
                            'class' => 'form-control prompt-select',
                            'required' => true
                        ]
                    )->label('Patient *') ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'dr_id')->dropDownList(
                        ArrayHelper::map(
                            TblDoctor::find()
                                ->joinWith('dept')
                                ->orderBy(['dr_id' => SORT_ASC])
                                ->all(),
                            'dr_id',
                            function($model) {
                                $deptName = $model->dept ? $model->dept->dept_name : 'General';
                                return $model->dr_id . ' - Dr. ' . $model->first_name . ' ' . $model->last_name .
                                       ' | ' . ($model->specialization ?? 'General') .
                                       ' | Dept: ' . $deptName .
                                       ' | Fee: ₱' . number_format($model->dr_fee, 2);
                            }
                        ),
                        [
                            'prompt' => '-- Select Doctor --',
                            'id' => 'appointment-dr_id',
                            'class' => 'form-control prompt-select',
                            'required' => true
                        ]
                    )->label('Doctor *') ?>
                </div>
            </div>

            <!-- DATE & TIME -->
            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'appointment_date')->input('date', [
                        'min' => date('Y-m-d'),
                        'required' => true
                    ])->label('Appointment Date *') ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'appointment_time')->input('time', [
                        'required' => true
                    ])->label('Appointment Time *') ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'status')->dropDownList([ 
                        'scheduled' => 'Scheduled',
                        'checked_in' => 'Checked In', 
                        'in_progress' => 'In Progress', 
                        'completed' => 'Completed', 
                        'cancelled' => 'Cancelled', 
                        'no_show' => 'No Show', 
                    ], [
                        'id' => 'appointment-status',
                        'class' => 'form-control prompt-select',
                        'required' => true
                    ])->label('Status *') ?>
                </div>
            </div>

            <!-- SYMPTOMS -->
            <?= $form->field($model, 'symptoms_list')->textarea([
                'rows' => 4,
                'placeholder' => 'Describe symptoms or reason for visit...'
            ])->label('Symptoms / Reason for Visit') ?>

            <!-- RECEPTIONIST (Auto-assigned or selected) -->
            <?php if ($isDirector): ?>
            <?= $form->field($model, 'recep_id')->dropDownList(
                ArrayHelper::map(
                    TblReceptionist::find()->orderBy(['recep_id' => SORT_ASC])->all(),
                    'recep_id',
                    function($model) {
                        return $model->recep_id . ' - ' . $model->first_name . ' ' . $model->last_name;
                    }
                ),
                [
                    'prompt' => '-- Select Receptionist --',
                    'class' => 'form-control prompt-select'
                ]
            )->label('Receptionist') ?>
            <?php else: ?>
                <?= $form->field($model, 'recep_id')->hiddenInput(['value' => $user->receptionist_id])->label(false) ?>
            <?php endif; ?>

            <div class="form-group mt-4">
                <?= Html::submitButton('<i class="fas fa-save"></i> ' . ($model->isNewRecord ? 'Create Appointment' : 'Update Appointment'), [
                    'class' => 'btn btn-success btn-lg'
                ]) ?>
                <?= Html::resetButton('<i class="fas fa-undo"></i> Reset', [
                    'class' => 'btn btn-secondary btn-lg',
                    'onclick' => 'resetForm()'
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<?php
$this->registerJs("
    // ==========================================
    // DROPDOWN PROMPT DISABLE
    // ==========================================
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
    
    // ==========================================
    // SHOW DOCTOR FEE WHEN DOCTOR IS SELECTED
    // ==========================================
    $('#appointment-dr_id').on('change', function() {
        var selectedOption = $(this).find('option:selected');
        var text = selectedOption.text();
        var feeMatch = text.match(/Fee: ₱([\\d,]+\\.\\d{2})/);
        if (feeMatch) {
            var fee = feeMatch[1].replace(/,/g, '');
            $('#doctor-fee-display').val('₱' + fee);
            
        } else {
            $('#doctor-fee-display').val('');
            
        }
    });
    
    // ==========================================
    // RESET FORM
    // ==========================================
    function resetForm() {
        setTimeout(function() {
            $('.prompt-select').each(function() {
                $(this).find('option[value=\"\"]').prop('disabled', false);
            });
        }, 100);
    }
    
    // ==========================================
    // INITIALIZATION
    // ==========================================
    $(document).ready(function() {
        $('.prompt-select').each(function() {
            disablePromptOption(this);
        });
        
        // Show fee if doctor already selected (edit mode)
        if ($('#appointment-dr_id').val()) {
            $('#appointment-dr_id').trigger('change');
        }
    });
");
?>