<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\TblAppointment;
use common\models\TblPatient;
use common\models\TblDoctor;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var app\models\TblLabTest $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="tbl-lab-test-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'appt_id')->dropDownList(
        ArrayHelper::map(
            TblAppointment::find()->orderBy(['appt_id' => SORT_ASC])->all(), 
            'appt_id', 
            function($model) { 
                return $model->appt_id . ' - Date: ' . $model->appointment_date . ' | Patient: ' . $model->patient_id . ' | Doctor: ' . $model->dr_id; 
            }
        ),
        ['prompt' => 'Select Appointment']
    ) ?>

    <?= $form->field($model, 'patient_id')->dropDownList(
        ArrayHelper::map(
            TblPatient::find()->orderBy(['patient_id' => SORT_ASC])->all(), 
            'patient_id', 
            function($model) { 
                return $model->patient_id . ' - ' . $model->last_name . ', ' . $model->first_name . ' ' . ($model->middle_name ?? ''); 
            }
        ),
        ['prompt' => 'Select Patient']
    ) ?>

    <?= $form->field($model, 'dr_id')->dropDownList(
        ArrayHelper::map(
            TblDoctor::find()->orderBy(['dr_id' => SORT_ASC])->all(), 
            'dr_id', 
            function($model) { 
                return $model->dr_id . ' - Dr. ' . $model->first_name . ' ' . $model->last_name . ' (' . ($model->specialization ?? 'General') . ')'; 
            }
        ),
        ['prompt' => 'Select Doctor']
    ) ?>

    <?= $form->field($model, 'test_name')->textInput(['maxlength' => true, 'placeholder' => 'Complete Blood Count (CBC)']) ?>

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
    ], ['prompt' => 'Select Category']) ?>

    <?= $form->field($model, 'status')->dropDownList([ 
        'ordered' => 'Ordered', 
        'collected' => 'Collected', 
        'processing' => 'Processing', 
        'completed' => 'Completed', 
        'cancelled' => 'Cancelled', 
    ], ['prompt' => 'Select Status']) ?>

    <?= $form->field($model, 'results')->textarea(['rows' => 4, 'placeholder' => 'Enter test results here...']) ?>

    <?= $form->field($model, 'is_abnormal')->checkbox(['label' => 'Mark as Abnormal Result']) ?>

    <?= $form->field($model, 'results_date')->input('datetime-local') ?>

    <?= $form->field($model, 'notes')->textarea(['rows' => 3, 'placeholder' => 'Additional notes...']) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>