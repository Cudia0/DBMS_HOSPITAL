<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ArrayDataProvider $dataProvider */

$this->title = 'My Prescriptions';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-prescription-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-striped table-hover'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'prescription_id',
            'appt_id',
            [
                'label' => 'Doctor',
                'value' => function($model) {
                    return isset($model['doctor_lname']) ? 'Dr. ' . $model['doctor_lname'] : 'N/A';
                },
            ],
            [
                'attribute' => 'prescription_date',
                'label' => 'Date',
                'format' => 'datetime',
            ],
            [
                'attribute' => 'duration_days',
                'label' => 'Duration',
                'value' => function($model) {
                    return $model['duration_days'] ? $model['duration_days'] . ' days' : 'N/A';
                },
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{view}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('<i class="fas fa-eye"></i>', $url, ['title' => 'View', 'class' => 'btn btn-primary btn-sm']);
                    },
                ],
                'urlCreator' => function ($action, $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'prescription_id' => $model['prescription_id']]);
                },
            ],
        ],
    ]); ?>

</div>