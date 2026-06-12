<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TblAppointment $model */

$this->title = 'Update Tbl Appointment: ' . $model->appt_id;
$this->params['breadcrumbs'][] = ['label' => 'Tbl Appointments', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->appt_id, 'url' => ['view', 'appt_id' => $model->appt_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tbl-appointment-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
