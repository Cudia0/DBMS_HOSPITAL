<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\TblPrescription $model */

$this->title = $model->prescription_id;
$this->params['breadcrumbs'][] = ['label' => 'Tbl Prescriptions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="tbl-prescription-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'prescription_id' => $model->prescription_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'prescription_id' => $model->prescription_id], [
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
            'prescription_id',
            'appt_id',

            'prescription_date',
            'dosage_instructions:ntext',
            'duration_days',
            'notes:ntext',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
