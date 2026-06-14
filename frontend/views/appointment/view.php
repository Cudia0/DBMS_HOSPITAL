<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var object $model */

$this->title = 'Appointment #' . $model->appt_id;
$this->params['breadcrumbs'][] = ['label' => 'My Appointments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Convert object to array for consistent access
$data = (array) $model;
?>
<div class="tbl-appointment-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'appt_id',
            [
                'label' => 'Doctor',
                'value' => isset($data['doctor_fname']) ? 'Dr. ' . $data['doctor_fname'] . ' ' . ($data['doctor_lname'] ?? '') . ' (' . ($data['specialization'] ?? 'General') . ')' : 'N/A',
            ],
            [
                'label' => 'Consultation Fee',
                'value' => isset($data['dr_fee']) ? '₱' . number_format($data['dr_fee'], 2) : 'N/A',
            ],
            [
                'attribute' => 'appointment_date',
                'value' => $model->appointment_date ? Yii::$app->formatter->asDate($model->appointment_date, 'long') : 'Not yet scheduled',
            ],
            [
                'attribute' => 'appointment_time',
                'value' => $model->appointment_time ? Yii::$app->formatter->asTime($model->appointment_time, 'short') : 'Not yet scheduled',
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function($model) {
                    if (empty($model->status)) {
                        return '<span class="badge bg-secondary">Pending Acceptance</span>';
                    }
                    $labels = [
                        'scheduled' => '<span class="badge bg-warning">Scheduled</span>',
                        'checked_in' => '<span class="badge bg-info">Checked In</span>',
                        'in_progress' => '<span class="badge bg-primary">In Progress</span>',
                        'completed' => '<span class="badge bg-success">Completed</span>',
                        'cancelled' => '<span class="badge bg-danger">Cancelled</span>',
                    ];
                    return $labels[$model->status] ?? $model->status;
                },
            ],
            'symptoms_list:ntext',
            'created_at:datetime',
        ],
    ]) ?>

</div>