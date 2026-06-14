<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\TblAppointment $model */

$this->title = 'Appointment #' . $model->appt_id;
$this->params['breadcrumbs'][] = ['label' => 'My Appointments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-appointment-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'appt_id',
            [
                'label' => 'Doctor',
                'value' => $model->doctor ? 'Dr. ' . $model->doctor->first_name . ' ' . $model->doctor->last_name . ' (' . ($model->doctor->specialization ?? 'General') . ')' : 'N/A',
            ],
            [
                'label' => 'Consultation Fee',
                'value' => $model->doctor ? '₱' . number_format($model->doctor->dr_fee, 2) : 'N/A',
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
                'value' => $model->getStatusLabel(),
            ],
            'symptoms_list:ntext',
            'created_at:datetime',
        ],
    ]) ?>

</div>