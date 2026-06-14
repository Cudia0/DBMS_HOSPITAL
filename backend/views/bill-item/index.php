<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ArrayDataProvider $dataProvider */

$this->title = 'Bill Items';
$this->params['breadcrumbs'][] = $this->title;

$user = Yii::$app->user->identity;
$isReceptionist = $user && $user->isReceptionist();
$isDirector = $user && $user->isDirector();
?>
<div class="tbl-bill-item-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fas fa-list"></i> View All Bills', ['bill/index'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-striped table-hover'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'bill_item_id',
            'bill_id',
            [
                'attribute' => 'item_type',
                'label' => 'Type',
                'format' => 'raw',
                'value' => function($model) {
                    $labels = [
                        'consultation' => '<span class="badge bg-primary">Consultation</span>',
                        'medicine' => '<span class="badge bg-success">Medicine</span>',
                        'lab_test' => '<span class="badge bg-warning text-dark">Lab Test</span>',
                        'procedure' => '<span class="badge bg-info">Procedure</span>',
                        'other' => '<span class="badge bg-secondary">Other</span>',
                    ];
                    return $labels[$model['item_type']] ?? $model['item_type'];
                },
            ],
            'description',
            'quantity',
            [
                'attribute' => 'unit_price',
                'label' => 'Unit Price',
                'value' => function($model) {
                    return '₱' . number_format($model['unit_price'], 2);
                },
            ],
            [
                'attribute' => 'total_price',
                'label' => 'Total',
                'format' => 'raw',
                'value' => function($model) {
                    return '<strong>₱' . number_format($model['total_price'], 2) . '</strong>';
                },
            ],
            'created_at:datetime',
            [
                'class' => ActionColumn::class,
                'template' => '{view} {update} {delete}',
                'visibleButtons' => [
                    'update' => function($model) use ($user) {
                        return $user && ($user->isDirector() || $user->isReceptionist());
                    },
                    'delete' => function($model) use ($user) {
                        return $user && $user->isDirector();
                    },
                ],
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('<i class="fas fa-eye"></i>', $url, ['title' => 'View', 'class' => 'btn btn-primary btn-sm']);
                    },
                    'update' => function ($url, $model) {
                        return Html::a('<i class="fas fa-edit"></i>', $url, ['title' => 'Edit', 'class' => 'btn btn-info btn-sm']);
                    },
                    'delete' => function ($url, $model) {
                        return Html::a('<i class="fas fa-trash"></i>', $url, [
                            'title' => 'Delete', 'class' => 'btn btn-danger btn-sm',
                            'data' => ['confirm' => 'Delete?', 'method' => 'post'],
                        ]);
                    },
                ],
                'urlCreator' => function ($action, $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'bill_item_id' => $model['bill_item_id']]);
                },
            ],
        ],
    ]); ?>

</div>