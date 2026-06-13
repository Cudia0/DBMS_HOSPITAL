<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\AppointmentSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="tbl-appointment-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'appt_id') ?>

    <?= $form->field($model, 'dr_id') ?>

    <?= $form->field($model, 'patient_id') ?>

    <?= $form->field($model, 'recep_id') ?>

    <?= $form->field($model, 'symptoms_list') ?>

    <?php // echo $form->field($model, 'appointment_date') ?>

    <?php // echo $form->field($model, 'appointment_time') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
