<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\AppointmentSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

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
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-striped table-hover'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'appt_id',
            [
                'label' => 'Doctor',
                'value' => function($model) {
                    return $model->doctor ? 'Dr. ' . $model->doctor->first_name . ' ' . $model->doctor->last_name : 'N/A';
                },
            ],
            [
                'attribute' => 'appointment_date',
                'format' => 'raw',
                'value' => function($model) {
                    return $model->appointment_date ? Yii::$app->formatter->asDate($model->appointment_date, 'medium') : '<span class="text-warning">Pending</span>';
                },
            ],
            [
                'attribute' => 'appointment_time',
                'format' => 'raw',
                'value' => function($model) {
                    return $model->appointment_time ? Yii::$app->formatter->asTime($model->appointment_time, 'short') : '<span class="text-warning">Pending</span>';
                },
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function($model) {
                    return $model->getStatusLabel();
                },
                'filter' => [
                    '' => 'Pending',
                    'scheduled' => 'Scheduled',
                    'checked_in' => 'Checked In',
                    'in_progress' => 'In Progress',
                    'completed' => 'Completed',
                    'cancelled' => 'Cancelled',
                ],
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{view}',
                'urlCreator' => function ($action, $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'appt_id' => $model->appt_id]);
                },
            ],
        ],
    ]); ?>

</div>