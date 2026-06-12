<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\TblPrescription $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="tbl-prescription-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'appt_id')->textInput() ?>

    <?= $form->field($model, 'med_id')->textInput() ?>

    <?= $form->field($model, 'dr_id')->textInput() ?>

    <?= $form->field($model, 'qty')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
