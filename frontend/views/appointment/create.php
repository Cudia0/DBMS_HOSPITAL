<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\TblAppointment $model */
/** @var common\models\TblPatient $patient */

$this->title = 'Book an Appointment';
$this->params['breadcrumbs'][] = ['label' => 'My Appointments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-appointment-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'patient' => $patient,
    ]) ?>

</div>