<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TblMedicine $model */

$this->title = 'Update Tbl Medicine: ' . $model->med_id;
$this->params['breadcrumbs'][] = ['label' => 'Tbl Medicines', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->med_id, 'url' => ['view', 'med_id' => $model->med_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tbl-medicine-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
