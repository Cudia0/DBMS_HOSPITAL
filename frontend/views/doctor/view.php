<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\TblDoctor $model */

$this->title = $model->dr_id;
$this->params['breadcrumbs'][] = ['label' => 'Tbl Doctors', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="tbl-doctor-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'dr_id' => $model->dr_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'dr_id' => $model->dr_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'dr_id',
            'first_name',
            'middle_name',
            'last_name',
            'dr_fee',
            'dept_id',
            'specialization',
            'certification',
        ],
    ]) ?>

</div>
