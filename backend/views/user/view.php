<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\User;

/** @var yii\web\View $this */
/** @var common\models\User $model */

$this->title = 'User: ' . $model->username;
$this->params['breadcrumbs'][] = ['label' => 'User Management', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$model->detectRole();
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fas fa-edit"></i> Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php if ($model->id !== Yii::$app->user->id): ?>
            <?php if ($model->status === User::STATUS_ACTIVE): ?>
                <?= Html::a('<i class="fas fa-ban"></i> Deactivate', ['deactivate', 'id' => $model->id], [
                    'class' => 'btn btn-warning',
                    'data' => ['confirm' => 'Deactivate this user?', 'method' => 'post'],
                ]) ?>
            <?php else: ?>
                <?= Html::a('<i class="fas fa-check-circle"></i> Activate', ['activate', 'id' => $model->id], [
                    'class' => 'btn btn-success',
                    'data' => ['confirm' => 'Activate this user?', 'method' => 'post'],
                ]) ?>
            <?php endif; ?>
            <?= Html::a('<i class="fas fa-trash"></i> Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => ['confirm' => 'Delete this user?', 'method' => 'post'],
            ]) ?>
        <?php endif; ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'username',
            'email:email',
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function($model) {
                    if ($model->status == User::STATUS_ACTIVE) {
                        return '<span class="badge bg-success">Active</span>';
                    } elseif ($model->status == User::STATUS_INACTIVE) {
                        return '<span class="badge bg-warning">Inactive</span>';
                    } else {
                        return '<span class="badge bg-danger">Deleted</span>';
                    }
                },
            ],
            [
                'label' => 'Detected Role',
                'format' => 'raw',
                'value' => function($model) {
                    $roleLabels = [
                        'director' => '<span class="badge bg-dark">Director</span>',
                        'doctor' => '<span class="badge bg-success">Doctor</span>',
                        'receptionist' => '<span class="badge bg-warning text-dark">Receptionist</span>',
                        'patient' => '<span class="badge bg-info">Patient</span>',
                    ];
                    return $roleLabels[$model->role] ?? '<span class="badge bg-secondary">Unknown</span>';
                },
            ],
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>