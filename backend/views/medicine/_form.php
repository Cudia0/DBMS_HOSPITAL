<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\TblMedicine $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="tbl-medicine-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'med_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'med_price')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
