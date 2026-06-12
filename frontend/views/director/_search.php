<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\DirectorSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="tbl-director-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'director_id') ?>

    <?= $form->field($model, 'full_name') ?>

    <?= $form->field($model, 'phone_num') ?>

    <?= $form->field($model, 'country_code') ?>

    <?= $form->field($model, 'email') ?>

    <?php // echo $form->field($model, 'recep_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
