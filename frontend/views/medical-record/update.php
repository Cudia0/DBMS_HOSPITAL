<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TblMedicalRecord $model */

$this->title = 'Update Tbl Medical Record: ' . $model->record_id;
$this->params['breadcrumbs'][] = ['label' => 'Tbl Medical Records', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->record_id, 'url' => ['view', 'record_id' => $model->record_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tbl-medical-record-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
