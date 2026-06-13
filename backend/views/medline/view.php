<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\TblMedline $model */

$this->title = $model->medline_id;
$this->params['breadcrumbs'][] = ['label' => 'Tbl Medlines', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="tbl-medline-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'medline_id' => $model->medline_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'medline_id' => $model->medline_id], [
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
            'medline_id',
            'prescription_id',
            'med_id',
            'qty',
            'dosage_per_intake',
            'frequency',
            'created_at',
        ],
    ]) ?>

</div>
