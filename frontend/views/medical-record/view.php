<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\TblMedicalRecord $model */

$this->title = $model->record_id;
$this->params['breadcrumbs'][] = ['label' => 'Tbl Medical Records', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="tbl-medical-record-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'record_id' => $model->record_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'record_id' => $model->record_id], [
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
            'record_id',
            'appt_id',
            'patient_id',
            'dr_id',
            'diagnosis:ntext',
            'treatment_plan:ntext',
            'vital_signs',
            'notes:ntext',
            'record_date',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
