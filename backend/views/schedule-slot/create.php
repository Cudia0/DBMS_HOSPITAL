<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TblScheduleSlot $model */

$this->title = 'Create Tbl Schedule Slot';
$this->params['breadcrumbs'][] = ['label' => 'Tbl Schedule Slots', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-schedule-slot-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
