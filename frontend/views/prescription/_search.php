<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\PrescriptionSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="tbl-prescription-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'prescription_id') ?>

    <?= $form->field($model, 'appt_id') ?>

    <?= $form->field($model, 'dr_id') ?>

    <?= $form->field($model, 'prescription_date') ?>

    <?= $form->field($model, 'dosage_instructions') ?>

    <?php // echo $form->field($model, 'duration_days') ?>

    <?php // echo $form->field($model, 'notes') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
