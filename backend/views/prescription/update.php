<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TblPrescription $model */

$this->title = 'Update Tbl Prescription: ' . $model->prescription_id;
$this->params['breadcrumbs'][] = ['label' => 'Tbl Prescriptions', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->prescription_id, 'url' => ['view', 'prescription_id' => $model->prescription_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tbl-prescription-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
