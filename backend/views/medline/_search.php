<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\MedlineSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="tbl-medline-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'medline_id') ?>

    <?= $form->field($model, 'prescription_id') ?>

    <?= $form->field($model, 'med_id') ?>

    <?= $form->field($model, 'qty') ?>

    <?= $form->field($model, 'dosage_per_intake') ?>

    <?php // echo $form->field($model, 'frequency') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
