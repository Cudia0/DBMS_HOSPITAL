<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ArrayDataProvider $dataProvider */

$this->title = 'Directors';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-director-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fas fa-plus"></i> Add Director', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-striped table-hover'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'director_id',
            [
                'label' => 'Full Name',
                'value' => function($model) {
                    return ($model['first_name'] ?? '') . ' ' . ($model['last_name'] ?? '');
                },
            ],
            'email',
            [
                'label' => 'Phone',
                'value' => function($model) {
                    return ($model['country_code'] ?? '') . ' ' . ($model['phone_num'] ?? '');
                },
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{view} {update} {delete}',
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
                            'data' => ['confirm' => 'Delete this director?', 'method' => 'post'],
                        ]);
                    },
                ],
                'urlCreator' => function ($action, $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'director_id' => $model['director_id']]);
                },
            ],
        ],
    ]); ?>

</div>