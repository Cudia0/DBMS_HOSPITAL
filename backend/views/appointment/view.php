<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\TblAppointment $model */

$this->title = $model->appt_id;
$this->params['breadcrumbs'][] = ['label' => 'Tbl Appointments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="tbl-appointment-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'appt_id' => $model->appt_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'appt_id' => $model->appt_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'appt_id',
            'dr_id',
            'patient_id',
            'recep_id',
            'symptoms_list:ntext',
            'appointment_date',
        ],
    ]) ?>

</div>
