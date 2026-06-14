<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var object $model */

$fullName = 'Dr. ' . ($model->first_name ?? '') . ' ' . ($model->last_name ?? '');
$this->title = $fullName;
$this->params['breadcrumbs'][] = ['label' => 'Doctors', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-doctor-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'dr_id' => $model->dr_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'dr_id' => $model->dr_id], [
            'class' => 'btn btn-danger',
            'data' => ['confirm' => 'Delete this doctor?', 'method' => 'post'],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'dr_id',
            'first_name',
            'middle_name',
            'last_name',
            'license_number',
            [
                'attribute' => 'dr_fee',
                'value' => $model->dr_fee ? '₱' . number_format($model->dr_fee, 2) : 'N/A',
            ],
            [
                'attribute' => 'dept_name',
                'label' => 'Department',
            ],
            'specialization',
            'certification',
            'email:email',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>