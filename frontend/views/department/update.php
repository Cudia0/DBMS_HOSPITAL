<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TblDepartment $model */

$this->title = 'Update Tbl Department: ' . $model->dept_id;
$this->params['breadcrumbs'][] = ['label' => 'Tbl Departments', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->dept_id, 'url' => ['view', 'dept_id' => $model->dept_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tbl-department-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
