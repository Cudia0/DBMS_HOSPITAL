<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use common\models\TblAppointment;

/** @var yii\web\View $this */
/** @var common\models\AppointmentSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

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
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-striped table-hover'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            
            'appt_id',
            [
                'attribute' => 'patient_id',
                'label' => 'Patient',
                'value' => function($model) {
                    return $model->patient ? $model->patient->getFullName() : 'N/A';
                },
            ],
            [
                'attribute' => 'dr_id',
                'label' => 'Doctor',
                'value' => function($model) {
                    return $model->doctor ? 'Dr. ' . $model->doctor->last_name : 'N/A';
                },
            ],
            [
                'attribute' => 'appointment_date',
                'format' => 'raw',
                'value' => function($model) {
                    if ($model->appointment_date) {
                        return Yii::$app->formatter->asDate($model->appointment_date, 'medium');
                    }
                    return '<span class="text-warning">Pending</span>';
                },
            ],
            [
                'attribute' => 'appointment_time',
                'format' => 'raw',
                'value' => function($model) {
                    if ($model->appointment_time) {
                        return Yii::$app->formatter->asTime($model->appointment_time, 'short');
                    }
                    return '<span class="text-warning">Pending</span>';
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
                    'no_show' => 'No Show',
                ],
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
                        return ($isReceptionist || $isDirector) && ($model->status === null || $model->status === '');
                    },
                    'reject' => function($model) use ($isReceptionist, $isDirector) {
                        return ($isReceptionist || $isDirector) && ($model->status === null || $model->status === '' || $model->status === 'scheduled');
                    },
                    'checkin' => function($model) use ($isReceptionist, $isDirector) {
                        return ($isReceptionist || $isDirector) && $model->status === 'scheduled';
                    },
                ],
                'buttons' => [
                    'accept' => function ($url, $model) {
                        return Html::a('<i class="fas fa-check-circle"></i>', ['view', 'appt_id' => $model->appt_id], [
                            'title' => 'Accept & Schedule',
                            'class' => 'btn btn-success btn-sm',
                        ]);
                    },
                    'reject' => function ($url, $model) {
                        return Html::a('<i class="fas fa-times-circle"></i>', ['view', 'appt_id' => $model->appt_id], [
                            'title' => 'Reject / Cancel',
                            'class' => 'btn btn-danger btn-sm',
                        ]);
                    },
                    'checkin' => function ($url, $model) {
                        return Html::a('<i class="fas fa-sign-in-alt"></i>', ['check-in', 'appt_id' => $model->appt_id], [
                            'title' => 'Check In Patient',
                            'data' => [
                                'confirm' => 'Check in this patient?',
                                'method' => 'post',
                            ],
                            'class' => 'btn btn-info btn-sm',
                        ]);
                    },
                ],
                'urlCreator' => function ($action, $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'appt_id' => $model->appt_id]);
                },
            ],
        ],
    ]); ?>

</div>