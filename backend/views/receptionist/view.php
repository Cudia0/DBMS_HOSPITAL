<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\User;

/** @var yii\web\View $this */
/** @var app\models\TblReceptionist $model */

$this->title = 'Receptionist: ' . $model->first_name . ' ' . $model->last_name;
$this->params['breadcrumbs'][] = ['label' => 'Receptionists', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Check if user account exists
$userAccount = User::find()->where(['email' => $model->email])->one();
?>
<div class="tbl-receptionist-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fas fa-edit"></i> Update', ['update', 'recep_id' => $model->recep_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('<i class="fas fa-trash"></i> Delete', ['delete', 'recep_id' => $model->recep_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this receptionist? The associated user account will also be deleted.',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'recep_id',
            [
                'attribute' => 'fullName',
                'label' => 'Full Name',
                'value' => $model->first_name . ' ' . ($model->middle_name ? $model->middle_name . ' ' : '') . $model->last_name,
            ],
            'email:email',
            [
                'attribute' => 'phone_num',
                'label' => 'Phone Number',
                'value' => ($model->country_code ? $model->country_code . ' ' : '') . $model->phone_num,
            ],
            [
                'attribute' => 'director_id',
                'label' => 'Assigned Director',
                'value' => $model->director ? $model->director->first_name . ' ' . $model->director->last_name : 'Not Assigned',
            ],
            'created_at',
            'updated_at',
            [
                'label' => 'User Account',
                'format' => 'raw',
                'value' => $userAccount 
                    ? '<span class="badge bg-success">Active</span> Username: <strong>' . Html::encode($userAccount->username) . '</strong>'
                    : '<span class="badge bg-danger">No Account</span>',
            ],
        ],
    ]) ?>

</div>