<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\TblAppointment $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="tbl-appointment-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'dr_id')->textInput() ?>

    <?= $form->field($model, 'patient_id')->textInput() ?>

    <?= $form->field($model, 'recep_id')->textInput() ?>

    <?= $form->field($model, 'symptoms_list')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'appointment_date')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
