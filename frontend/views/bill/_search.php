<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\BillSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="tbl-bill-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'bill_id') ?>

    <?= $form->field($model, 'appt_id') ?>

    <?= $form->field($model, 'payment_status') ?>

    <?= $form->field($model, 'qty') ?>

    <?= $form->field($model, 'dr_fee') ?>

    <?php // echo $form->field($model, 'totalm_price') ?>

    <?php // echo $form->field($model, 'total_amount') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
