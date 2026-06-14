<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\PrescriptionSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'My Prescriptions';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-prescription-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-striped table-hover'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'prescription_id',
            'appt_id',
            [
                'label' => 'Doctor',
                'value' => function($model) {
                    return $model->doctor ? 'Dr. ' . $model->doctor->last_name : 'N/A';
                },
            ],
            [
                'attribute' => 'prescription_date',
                'format' => 'datetime',
            ],
            [
                'attribute' => 'duration_days',
                'value' => function($model) {
                    return $model->duration_days ? $model->duration_days . ' days' : 'N/A';
                },
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{view}',
                'urlCreator' => function ($action, $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'prescription_id' => $model->prescription_id]);
                },
            ],
        ],
    ]); ?>

</div>