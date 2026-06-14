<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ArrayDataProvider $dataProvider */

$this->title = 'My Medical Records';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-medical-record-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-striped table-hover'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'record_id',
            'appt_id',
            [
                'label' => 'Doctor',
                'value' => function($model) {
                    return isset($model['doctor_lname']) ? 'Dr. ' . $model['doctor_lname'] : 'N/A';
                },
            ],
            [
                'attribute' => 'diagnosis',
                'label' => 'Diagnosis',
                'value' => function($model) {
                    return !empty($model['diagnosis']) ? substr($model['diagnosis'], 0, 50) . '...' : '<span class="text-muted">No diagnosis</span>';
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'record_date',
                'label' => 'Date',
                'format' => 'datetime',
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
                    return Url::toRoute([$action, 'record_id' => $model['record_id']]);
                },
            ],
        ],
    ]); ?>

</div>