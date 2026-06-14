<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var common\models\User $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'username')->textInput([
        'maxlength' => true,
        'required' => true
    ])->label('Username *') ?>

    <?= $form->field($model, 'email')->textInput([
        'type' => 'email',
        'maxlength' => true,
        'required' => true
    ])->label('Email *') ?>

    <?= $form->field($model, 'password_hash')->passwordInput([
        'placeholder' => $model->isNewRecord ? 'Enter password' : 'Leave blank to keep current',
        'value' => '',
    ])->label($model->isNewRecord ? 'Password *' : 'New Password (leave blank to keep current)') ?>

    <?= $form->field($model, 'status')->dropDownList([
        \common\models\User::STATUS_ACTIVE => 'Active',
        \common\models\User::STATUS_INACTIVE => 'Inactive',
    ])->label('Status') ?>

    <div class="form-group mt-4">
        <?= Html::submitButton('<i class="fas fa-save"></i> ' . ($model->isNewRecord ? 'Create User' : 'Update User'), [
            'class' => 'btn btn-success btn-lg'
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
