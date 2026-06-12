<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\TblDoctor $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="tbl-doctor-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'middle_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'dr_fee')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'dept_id')->textInput() ?>

    <?= $form->field($model, 'specialization')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'certification')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
