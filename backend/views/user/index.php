<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use common\models\User;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'User Management';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fas fa-user-plus"></i> Create User', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-striped table-hover'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            
            'id',
            'username',
            'email:email',
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function($model) {
                    if ($model->status === User::STATUS_ACTIVE) {
                        return '<span class="badge bg-success">Active</span>';
                    } elseif ($model->status === User::STATUS_INACTIVE) {
                        return '<span class="badge bg-warning">Inactive</span>';
                    } else {
                        return '<span class="badge bg-danger">Deleted</span>';
                    }
                },
                'filter' => [
                    User::STATUS_ACTIVE => 'Active',
                    User::STATUS_INACTIVE => 'Inactive',
                    User::STATUS_DELETED => 'Deleted',
                ],
            ],
            [
                'label' => 'Role',
                'format' => 'raw',
                'value' => function($model) {
                    $model->detectRole();
                    $roleLabels = [
                        'director' => '<span class="badge bg-dark">Director</span>',
                        'doctor' => '<span class="badge bg-success">Doctor</span>',
                        'receptionist' => '<span class="badge bg-warning text-dark">Receptionist</span>',
                        'patient' => '<span class="badge bg-info">Patient</span>',
                    ];
                    return $roleLabels[$model->role] ?? '<span class="badge bg-secondary">Unknown</span>';
                },
            ],
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
                'label' => 'Created',
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{view} {update} {activate} {deactivate} {delete}',
                'visibleButtons' => [
                    'activate' => function($model) {
                        return $model->status !== User::STATUS_ACTIVE;
                    },
                    'deactivate' => function($model) {
                        return $model->status === User::STATUS_ACTIVE && $model->id !== Yii::$app->user->id;
                    },
                    'delete' => function($model) {
                        return $model->id !== Yii::$app->user->id;
                    },
                ],
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('<i class="fas fa-eye"></i>', $url, [
                            'title' => 'View',
                            'class' => 'btn btn-primary btn-sm',
                        ]);
                    },
                    'update' => function ($url, $model) {
                        return Html::a('<i class="fas fa-edit"></i>', $url, [
                            'title' => 'Update',
                            'class' => 'btn btn-info btn-sm',
                        ]);
                    },
                    'activate' => function ($url, $model) {
                        return Html::a('<i class="fas fa-check-circle"></i>', $url, [
                            'title' => 'Activate',
                            'data' => [
                                'confirm' => 'Activate this user account?',
                                'method' => 'post',
                            ],
                            'class' => 'btn btn-success btn-sm',
                        ]);
                    },
                    'deactivate' => function ($url, $model) {
                        return Html::a('<i class="fas fa-ban"></i>', $url, [
                            'title' => 'Deactivate',
                            'data' => [
                                'confirm' => 'Deactivate this user account?',
                                'method' => 'post',
                            ],
                            'class' => 'btn btn-warning btn-sm',
                        ]);
                    },
                    'delete' => function ($url, $model) {
                        return Html::a('<i class="fas fa-trash"></i>', $url, [
                            'title' => 'Delete',
                            'data' => [
                                'confirm' => 'Are you sure you want to delete this user?',
                                'method' => 'post',
                            ],
                            'class' => 'btn btn-danger btn-sm',
                        ]);
                    },
                ],
                'urlCreator' => function ($action, $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                },
            ],
        ],
    ]); ?>

</div>
