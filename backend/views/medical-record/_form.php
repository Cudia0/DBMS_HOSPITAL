<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\TblAppointment;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

/** @var yii\web\View $this */
/** @var app\models\TblMedicalRecord $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="tbl-medical-record-form">
    <div class="card">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0"><i class="fas fa-stethoscope"></i> Medical Consultation Record</h4>
        </div>
        <div class="card-body">
            <?php $form = ActiveForm::begin(['options' => ['id' => 'medical-record-form']]); ?>

            <?= $form->field($model, 'appt_id')->dropDownList(
                ArrayHelper::map(
                    TblAppointment::find()
                        ->where(['status' => ['checked_in', 'in_progress']])
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
                [
                    'prompt' => '-- Select Appointment --',
                    'id' => 'medical-appt_id'
                ]
            )->label('Appointment *') ?>

            <?= $form->field($model, 'vital_signs')->textInput([
                'maxlength' => true,
                'placeholder' => 'BP: 120/80, HR: 72 bpm, Temp: 98.6°F, RR: 16, O2: 98%'
            ])->label('Vital Signs') ?>

            <?= $form->field($model, 'diagnosis')->textarea([
                'rows' => 4,
                'placeholder' => 'Enter diagnosis...'
            ])->label('Diagnosis *') ?>

            <?= $form->field($model, 'treatment_plan')->textarea([
                'rows' => 4,
                'placeholder' => 'Enter treatment plan...'
            ])->label('Treatment Plan') ?>

            <?= $form->field($model, 'notes')->textarea([
                'rows' => 3,
                'placeholder' => 'Additional notes...'
            ])->label('Notes') ?>

            <div class="form-group">
                <?= Html::submitButton('<i class="fas fa-save"></i> Save Medical Record', [
                    'class' => 'btn btn-primary btn-lg'
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>