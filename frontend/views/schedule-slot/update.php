<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TblScheduleSlot $model */

$this->title = 'Update Tbl Schedule Slot: ' . $model->slot_id;
$this->params['breadcrumbs'][] = ['label' => 'Tbl Schedule Slots', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->slot_id, 'url' => ['view', 'slot_id' => $model->slot_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tbl-schedule-slot-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
