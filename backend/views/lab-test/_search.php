<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\LabTestSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="tbl-lab-test-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'test_id') ?>

    <?= $form->field($model, 'appt_id') ?>

    <?= $form->field($model, 'patient_id') ?>

    <?= $form->field($model, 'dr_id') ?>

    <?= $form->field($model, 'test_name') ?>

    <?php // echo $form->field($model, 'test_category') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'results') ?>

    <?php // echo $form->field($model, 'is_abnormal') ?>

    <?php // echo $form->field($model, 'ordered_date') ?>

    <?php // echo $form->field($model, 'results_date') ?>

    <?php // echo $form->field($model, 'notes') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
