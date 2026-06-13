<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\MedicalRecordSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="tbl-medical-record-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'record_id') ?>

    <?= $form->field($model, 'appt_id') ?>

    <?= $form->field($model, 'patient_id') ?>

    <?= $form->field($model, 'dr_id') ?>

    <?= $form->field($model, 'diagnosis') ?>

    <?php // echo $form->field($model, 'treatment_plan') ?>

    <?php // echo $form->field($model, 'vital_signs') ?>

    <?php // echo $form->field($model, 'notes') ?>

    <?php // echo $form->field($model, 'record_date') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
