<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TblMedline $model */

$this->title = 'Update Tbl Medline: ' . $model->medline_id;
$this->params['breadcrumbs'][] = ['label' => 'Tbl Medlines', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->medline_id, 'url' => ['view', 'medline_id' => $model->medline_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tbl-medline-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
