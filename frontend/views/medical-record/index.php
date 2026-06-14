<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\MedicalRecordSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'My Medical Records';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-medical-record-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-striped table-hover'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'record_id',
            'appt_id',
            [
                'label' => 'Doctor',
                'value' => function($model) {
                    return $model->doctor ? 'Dr. ' . $model->doctor->last_name : 'N/A';
                },
            ],
            [
                'attribute' => 'diagnosis',
                'value' => function($model) {
                    return $model->diagnosis ? substr($model->diagnosis, 0, 50) . '...' : '<span class="text-muted">No diagnosis</span>';
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'record_date',
                'format' => 'datetime',
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{view}',
                'urlCreator' => function ($action, $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'record_id' => $model->record_id]);
                },
            ],
        ],
    ]); ?>

</div>