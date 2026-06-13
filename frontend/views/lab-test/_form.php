<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\TblAppointment;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var app\models\TblLabTest $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="tbl-lab-test-form">
    <div class="card">
        <div class="card-header bg-warning text-white">
            <h4 class="mb-0"><i class="fas fa-flask"></i> Laboratory Test</h4>
        </div>
        <div class="card-body">
            <?php $form = ActiveForm::begin(['options' => ['id' => 'lab-test-form']]); ?>

            <?= $form->field($model, 'appt_id')->dropDownList(
                ArrayHelper::map(
                    TblAppointment::find()
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
            )->label('Appointment') ?>

            <?= $form->field($model, 'test_name')->textInput([
                'maxlength' => true,
                'placeholder' => 'Complete Blood Count (CBC)'
            ])->label('Test Name *') ?>

            <?= $form->field($model, 'test_category')->dropDownList([ 
                'hematology' => 'Hematology',
                'chemistry' => 'Chemistry', 
                'microbiology' => 'Microbiology', 
                'immunology' => 'Immunology', 
                'radiology' => 'Radiology', 
                'cardiology' => 'Cardiology', 
                'endocrinology' => 'Endocrinology',
                'urinalysis' => 'Urinalysis',
                'pathology' => 'Pathology',
                'other' => 'Other',
            ], ['prompt' => '-- Select Category --']) ?>

            <?= $form->field($model, 'status')->dropDownList([ 
                'ordered' => 'Ordered', 
                'collected' => 'Collected', 
                'processing' => 'Processing', 
                'completed' => 'Completed', 
                'cancelled' => 'Cancelled', 
            ]) ?>

            <?= $form->field($model, 'results')->textarea([
                'rows' => 4,
                'placeholder' => 'Enter test results here...'
            ]) ?>

            <?= $form->field($model, 'is_abnormal')->checkbox(['label' => 'Mark as Abnormal Result']) ?>

            <?= $form->field($model, 'results_date')->input('datetime-local') ?>

            <?= $form->field($model, 'notes')->textarea([
                'rows' => 3,
                'placeholder' => 'Additional notes...'
            ]) ?>

            <div class="form-group">
                <?= Html::submitButton('<i class="fas fa-save"></i> Save Lab Test', [
                    'class' => 'btn btn-success btn-lg'
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>