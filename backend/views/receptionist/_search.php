<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\ReceptionistSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="tbl-receptionist-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'recep_id') ?>

    <?= $form->field($model, 'Full_Name') ?>

    <?= $form->field($model, 'Email') ?>

    <?= $form->field($model, 'phone_num') ?>

    <?= $form->field($model, 'country_code') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
