<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\TblBill $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="tbl-bill-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'appt_id')->textInput() ?>

    <?= $form->field($model, 'payment_status')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'qty')->textInput() ?>

    <?= $form->field($model, 'dr_fee')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'totalm_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'total_amount')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
