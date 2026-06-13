<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TblPatient $model */

$this->title = 'Update Tbl Patient: ' . $model->patient_id;
$this->params['breadcrumbs'][] = ['label' => 'Tbl Patients', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->patient_id, 'url' => ['view', 'patient_id' => $model->patient_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tbl-patient-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
