<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TblPrescription $model */

$this->title = 'Create Tbl Prescription';
$this->params['breadcrumbs'][] = ['label' => 'Tbl Prescriptions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-prescription-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
