<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TblLabTest $model */

$this->title = 'Update Tbl Lab Test: ' . $model->test_id;
$this->params['breadcrumbs'][] = ['label' => 'Tbl Lab Tests', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->test_id, 'url' => ['view', 'test_id' => $model->test_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tbl-lab-test-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
