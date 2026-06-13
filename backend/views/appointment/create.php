<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TblAppointment $model */

$this->title = 'Create Tbl Appointment';
$this->params['breadcrumbs'][] = ['label' => 'Tbl Appointments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-appointment-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
