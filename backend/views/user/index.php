<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use common\models\User;

/** @var yii\web\View $this */
/** @var yii\data\ArrayDataProvider $dataProvider */

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
                'label' => 'Status',
                'format' => 'raw',
                'value' => function($model) {
                    if ($model['status'] == User::STATUS_ACTIVE) {
                        return '<span class="badge bg-success">Active</span>';
                    } elseif ($model['status'] == User::STATUS_INACTIVE) {
                        return '<span class="badge bg-warning">Inactive</span>';
                    } else {
                        return '<span class="badge bg-danger">Deleted</span>';
                    }
                },
            ],
            [
                'attribute' => 'created_at',
                'label' => 'Created',
                'format' => 'datetime',
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{view} {update} {activate} {deactivate} {delete}',
                'visibleButtons' => [
                    'activate' => function($model) {
                        return $model['status'] != User::STATUS_ACTIVE;
                    },
                    'deactivate' => function($model) {
                        return $model['status'] == User::STATUS_ACTIVE && $model['id'] != Yii::$app->user->id;
                    },
                    'delete' => function($model) {
                        return $model['id'] != Yii::$app->user->id;
                    },
                ],
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('<i class="fas fa-eye"></i>', $url, ['title' => 'View', 'class' => 'btn btn-primary btn-sm']);
                    },
                    'update' => function ($url, $model) {
                        return Html::a('<i class="fas fa-edit"></i>', $url, ['title' => 'Update', 'class' => 'btn btn-info btn-sm']);
                    },
                    'activate' => function ($url, $model) {
                        return Html::a('<i class="fas fa-check-circle"></i>', $url, [
                            'title' => 'Activate', 'class' => 'btn btn-success btn-sm',
                            'data' => ['confirm' => 'Activate this user?', 'method' => 'post'],
                        ]);
                    },
                    'deactivate' => function ($url, $model) {
                        return Html::a('<i class="fas fa-ban"></i>', $url, [
                            'title' => 'Deactivate', 'class' => 'btn btn-warning btn-sm',
                            'data' => ['confirm' => 'Deactivate this user?', 'method' => 'post'],
                        ]);
                    },
                    'delete' => function ($url, $model) {
                        return Html::a('<i class="fas fa-trash"></i>', $url, [
                            'title' => 'Delete', 'class' => 'btn btn-danger btn-sm',
                            'data' => ['confirm' => 'Delete this user?', 'method' => 'post'],
                        ]);
                    },
                ],
                'urlCreator' => function ($action, $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model['id']]);
                },
            ],
        ],
    ]); ?>

</div>