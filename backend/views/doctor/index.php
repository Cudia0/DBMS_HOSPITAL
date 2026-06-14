<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ArrayDataProvider $dataProvider */

$this->title = 'Doctors';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-doctor-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fas fa-plus"></i> Add Doctor', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-striped table-hover'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'dr_id',
            [
                'label' => 'Full Name',
                'value' => function($model) {
                    return 'Dr. ' . ($model['first_name'] ?? '') . ' ' . ($model['last_name'] ?? '');
                },
            ],
            'specialization',
            'license_number',
            [
                'attribute' => 'dr_fee',
                'label' => 'Fee',
                'value' => function($model) {
                    return $model['dr_fee'] ? '₱' . number_format($model['dr_fee'], 2) : 'N/A';
                },
            ],
            [
                'attribute' => 'dept_name',
                'label' => 'Department',
            ],
            'email',
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
                            'data' => ['confirm' => 'Delete this doctor?', 'method' => 'post'],
                        ]);
                    },
                ],
                'urlCreator' => function ($action, $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'dr_id' => $model['dr_id']]);
                },
            ],
        ],
    ]); ?>

</div>