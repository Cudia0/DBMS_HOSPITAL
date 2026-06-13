<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\TblDepartment;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/** @var app\models\TblDoctor $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="tbl-doctor-form">
    <div class="card">
        <div class="card-header bg-success text-white">
            <h4 class="mb-0"><i class="fas fa-user-md"></i> Doctor Information</h4>
        </div>
        <div class="card-body">
            <?php $form = ActiveForm::begin(); ?>

            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'middle_name')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-4">
                    <?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>
                </div>
            </div>

            <?= $form->field($model, 'license_number')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'dr_fee')->textInput([
                'type' => 'number', 
                'step' => '0.01',
                'placeholder' => '500.00'
            ]) ?>

            <?= $form->field($model, 'dept_id')->dropDownList(
                ArrayHelper::map(
                    TblDepartment::find()->orderBy(['dept_id' => SORT_ASC])->all(), 
                    'dept_id', 
                    function($model) { 
                        return $model->dept_id . ' - ' . $model->dept_name; 
                    }
                ),
                ['prompt' => 'Select Department']
            ) ?>

            <?= $form->field($model, 'specialization')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'certification')->textInput(['maxlength' => true]) ?>

            <div class="form-group">
                <?= Html::submitButton('<i class="fas fa-save"></i> Save Doctor', [
                    'class' => 'btn btn-success'
                ]) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>