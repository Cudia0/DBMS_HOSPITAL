<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TblDoctor $model */

$this->title = 'Update Tbl Doctor: ' . $model->dr_id;
$this->params['breadcrumbs'][] = ['label' => 'Tbl Doctors', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->dr_id, 'url' => ['view', 'dr_id' => $model->dr_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tbl-doctor-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
