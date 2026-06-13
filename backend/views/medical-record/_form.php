<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\TblAppointment;
use common\models\TblPatient;
use common\models\TblDoctor;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\TblMedicalRecord $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="tbl-medical-record-form">
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0"><i class="fas fa-stethoscope"></i> Medical Consultation Record</h4>
        </div>
        <div class="card-body">
            <?php $form = ActiveForm::begin([
                'options' => ['id' => 'medical-record-form']
            ]); ?>

            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'appt_id')->dropDownList(
                        ArrayHelper::map(
                            TblAppointment::find()
                                ->where(['status' => ['checked_in', 'in_progress']])
                                ->orderBy(['appt_id' => SORT_ASC])
                                ->all(), 
                            'appt_id', 
                            function($model) { 
                                return $model->appt_id . ' - Date: ' . $model->appointment_date . 
                                       ' | Patient: ' . ($model->patient ? $model->patient->last_name . ', ' . $model->patient->first_name : 'N/A');
                            }
                        ),
                        [
                            'prompt' => '-- Select Appointment --',
                            'id' => 'medical-appt_id',
                            'onchange' => 'loadPatientAndDoctor()'
                        ]
                    ) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'patient_id')->dropDownList(
                        ArrayHelper::map(
                            TblPatient::find()->orderBy(['patient_id' => SORT_ASC])->all(), 
                            'patient_id', 
                            function($model) { 
                                return $model->patient_id . ' - ' . $model->last_name . ', ' . $model->first_name; 
                            }
                        ),
                        [
                            'prompt' => '-- Select Patient --',
                            'id' => 'medical-patient_id'
                        ]
                    ) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'dr_id')->dropDownList(
                        ArrayHelper::map(
                            TblDoctor::find()->orderBy(['dr_id' => SORT_ASC])->all(), 
                            'dr_id', 
                            function($model) { 
                                return $model->dr_id . ' - Dr. ' . $model->last_name . ' (' . ($model->specialization ?? 'General') . ')'; 
                            }
                        ),
                        [
                            'prompt' => '-- Select Doctor --',
                            'id' => 'medical-dr_id'
                        ]
                    ) ?>
                </div>
            </div>

            <?= $form->field($model, 'vital_signs')->textInput([
                'maxlength' => true,
                'placeholder' => 'BP: 120/80, HR: 72 bpm, Temp: 98.6°F, RR: 16, O2: 98%'
            ]) ?>

            <?= $form->field($model, 'diagnosis')->textarea([
                'rows' => 4,
                'placeholder' => 'Enter diagnosis...'
            ]) ?>

            <?= $form->field($model, 'treatment_plan')->textarea([
                'rows' => 4,
                'placeholder' => 'Enter treatment plan...'
            ]) ?>

            <?= $form->field($model, 'notes')->textarea([
                'rows' => 3,
                'placeholder' => 'Additional notes...'
            ]) ?>

            <div class="form-group">
                <?= Html::submitButton('<i class="fas fa-save"></i> Save Medical Record', [
                    'class' => 'btn btn-primary btn-lg'
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-md-4">
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-prescription"></i> Prescription</h5>
                </div>
                <div class="card-body text-center">
                    <?= Html::a('Create Prescription', ['prescription/create'], [
                        'class' => 'btn btn-info btn-lg btn-block'
                    ]) ?>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-warning">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-flask"></i> Lab Test</h5>
                </div>
                <div class="card-body text-center">
                    <?= Html::a('Order Lab Test', ['lab-test/create'], [
                        'class' => 'btn btn-warning btn-lg btn-block'
                    ]) ?>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-file-invoice-dollar"></i> Bill</h5>
                </div>
                <div class="card-body text-center">
                    <?= Html::a('Generate Bill', ['bill/create'], [
                        'class' => 'btn btn-success btn-lg btn-block'
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs("
    function loadPatientAndDoctor() {
        var apptId = $('#medical-appt_id').val();
        if (apptId) {
            $.ajax({
                url: '" . Url::to(['appointment/get-details']) . "',
                type: 'GET',
                data: {id: apptId},
                dataType: 'json',
                success: function(data) {
                    if (data.success) {
                        $('#medical-patient_id').val(data.patient_id);
                        $('#medical-dr_id').val(data.dr_id);
                    }
                }
            });
        }
    }
");
?>