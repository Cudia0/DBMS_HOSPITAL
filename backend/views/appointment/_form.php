<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\TblDoctor;
use common\models\TblPatient;
use common\models\TblReceptionist;
use common\models\TblDepartment;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var app\models\TblAppointment $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="tbl-appointment-form">
    <div class="card">
        <div class="card-header bg-info text-white">
            <h4 class="mb-0"><i class="fas fa-calendar-plus"></i> Schedule Appointment</h4>
        </div>
        <div class="card-body">
            <?php $form = ActiveForm::begin([
                'options' => ['id' => 'appointment-form'],
            ]); ?>

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
                            'class' => 'form-control prompt-select'
                        ]
                    ) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'dr_id')->dropDownList(
                        ArrayHelper::map(
                            TblDoctor::find()
                                ->orderBy(['dr_id' => SORT_ASC])
                                ->all(), 
                            'dr_id', 
                            function($model) { 
                                return $model->dr_id . ' - Dr. ' . $model->first_name . ' ' . $model->last_name . 
                                       ' | ' . ($model->specialization ?? 'General') . 
                                       ' | Fee: ₱' . number_format($model->dr_fee, 2);
                            }
                        ),
                        [
                            'prompt' => '-- Select Doctor --',
                            'id' => 'appointment-dr_id',
                            'class' => 'form-control prompt-select'
                        ]
                    ) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
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
                    ) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'status')->dropDownList([ 
                        'scheduled' => 'Scheduled', 
                        'checked_in' => 'Checked In', 
                        'in_progress' => 'In Progress', 
                        'completed' => 'Completed', 
                        'cancelled' => 'Cancelled', 
                        'no_show' => 'No Show', 
                    ], [
                        'prompt' => '-- Select Status --',
                        'id' => 'appointment-status',
                        'class' => 'form-control prompt-select'
                    ]) ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'appointment_date')->input('date', [
                        'min' => date('Y-m-d')
                    ]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'appointment_time')->input('time') ?>
                </div>
            </div>

            <?= $form->field($model, 'symptoms_list')->textarea([
                'rows' => 4,
                'placeholder' => 'Describe symptoms or reason for visit...'
            ]) ?>

            <div class="form-group">
                <?= Html::submitButton('<i class="fas fa-calendar-check"></i> Book Appointment', [
                    'class' => 'btn btn-success btn-lg'
                ]) ?>
                <?= Html::resetButton('<i class="fas fa-undo"></i> Reset', [
                    'class' => 'btn btn-secondary btn-lg'
                ]) ?>
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
    
    $(document).ready(function() {
        $('.prompt-select').each(function() {
            disablePromptOption(this);
        });
    });
");
?>