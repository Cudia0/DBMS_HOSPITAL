<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ArrayDataProvider $dataProvider */

$this->title = 'Appointments';
$this->params['breadcrumbs'][] = $this->title;

$user = Yii::$app->user->identity;
$isReceptionist = $user && $user->isReceptionist();
$isDirector = $user && $user->isDirector();
$isDoctor = $user && $user->isDoctor();
?>
<div class="tbl-appointment-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if ($isDirector || $isReceptionist): ?>
            <?= Html::a('<i class="fas fa-plus"></i> Create Appointment', ['create'], ['class' => 'btn btn-success']) ?>
        <?php endif; ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-striped table-hover'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'appt_id',
                'label' => 'ID',
            ],
            [
                'label' => 'Patient',
                'value' => function($model) {
                    return ($model['patient_fname'] ?? '') . ' ' . ($model['patient_lname'] ?? 'N/A');
                },
            ],
            [
                'label' => 'Doctor',
                'value' => function($model) {
                    return isset($model['doctor_lname']) ? 'Dr. ' . $model['doctor_lname'] : 'N/A';
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
                        'no_show' => '<span class="badge bg-dark">No Show</span>',
                    ];
                    return $labels[$model['status']] ?? $model['status'];
                },
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{view} {update} {delete} {accept} {reject} {checkin}',
                'visibleButtons' => [
                    'update' => function($model) use ($isDirector, $isReceptionist) {
                        return $isDirector || $isReceptionist;
                    },
                    'delete' => function($model) use ($isDirector) {
                        return $isDirector;
                    },
                    'accept' => function($model) use ($isReceptionist, $isDirector) {
                        return ($isReceptionist || $isDirector) && empty($model['status']);
                    },
                    'reject' => function($model) use ($isReceptionist, $isDirector) {
                        return ($isReceptionist || $isDirector) && (empty($model['status']) || $model['status'] === 'scheduled');
                    },
                    'checkin' => function($model) use ($isReceptionist, $isDirector) {
                        return ($isReceptionist || $isDirector) && $model['status'] === 'scheduled';
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
                    'accept' => function ($url, $model) {
                        return Html::a('<i class="fas fa-check-circle"></i>', ['view', 'appt_id' => $model['appt_id']], ['title' => 'Accept', 'class' => 'btn btn-success btn-sm']);
                    },
                    'reject' => function ($url, $model) {
                        return Html::a('<i class="fas fa-times-circle"></i>', ['view', 'appt_id' => $model['appt_id']], ['title' => 'Reject', 'class' => 'btn btn-danger btn-sm']);
                    },
                    'checkin' => function ($url, $model) {
                        return Html::a('<i class="fas fa-sign-in-alt"></i>', $url, [
                            'title' => 'Check In', 'class' => 'btn btn-info btn-sm',
                            'data' => ['confirm' => 'Check in patient?', 'method' => 'post'],
                        ]);
                    },
                ],
                'urlCreator' => function ($action, $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'appt_id' => $model['appt_id']]);
                },
            ],
        ],
    ]); ?>

</div>