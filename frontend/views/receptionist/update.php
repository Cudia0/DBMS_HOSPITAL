<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TblReceptionist $model */

$this->title = 'Update Tbl Receptionist: ' . $model->recep_id;
$this->params['breadcrumbs'][] = ['label' => 'Tbl Receptionists', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->recep_id, 'url' => ['view', 'recep_id' => $model->recep_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tbl-receptionist-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
