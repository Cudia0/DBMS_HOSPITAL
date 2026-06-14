<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ArrayDataProvider $dataProvider */

$this->title = 'My Appointments';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-appointment-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fas fa-calendar-plus"></i> Book Appointment', ['create'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-striped table-hover'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'appt_id',
            [
                'label' => 'Doctor',
                'value' => function($model) {
                    return isset($model['doctor_fname']) ? 'Dr. ' . $model['doctor_fname'] . ' ' . ($model['doctor_lname'] ?? '') : 'N/A';
                },
            ],
            [
                'attribute' => 'appointment_date',
                'label' => 'Date',
                'format' => 'raw',
                'value' => function($model) {
                    return $model['appointment_date'] ? Yii::$app->formatter->asDate($model['appointment_date'], 'medium') : '<span class="text-warning">Pending</span>';
                },
            ],
            [
                'attribute' => 'appointment_time',
                'label' => 'Time',
                'format' => 'raw',
                'value' => function($model) {
                    return $model['appointment_time'] ? Yii::$app->formatter->asTime($model['appointment_time'], 'short') : '<span class="text-warning">Pending</span>';
                },
            ],
            [
                'attribute' => 'status',
                'label' => 'Status',
                'format' => 'raw',
                'value' => function($model) {
                    if (empty($model['status'])) {
                        return '<span class="badge bg-secondary">Pending</span>';
                    }
                    $labels = [
                        'scheduled' => '<span class="badge bg-warning">Scheduled</span>',
                        'checked_in' => '<span class="badge bg-info">Checked In</span>',
                        'in_progress' => '<span class="badge bg-primary">In Progress</span>',
                        'completed' => '<span class="badge bg-success">Completed</span>',
                        'cancelled' => '<span class="badge bg-danger">Cancelled</span>',
                    ];
                    return $labels[$model['status']] ?? $model['status'];
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
                    return Url::toRoute([$action, 'appt_id' => $model['appt_id']]);
                },
            ],
        ],
    ]); ?>

</div>