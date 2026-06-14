<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var object $model */

$this->title = $model->dept_name;
$this->params['breadcrumbs'][] = ['label' => 'Departments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-department-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'dept_id' => $model->dept_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'dept_id' => $model->dept_id], [
            'class' => 'btn btn-danger',
            'data' => ['confirm' => 'Delete?', 'method' => 'post'],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'dept_id',
            'dept_name',
            'operating_days',
            'office_hours',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>