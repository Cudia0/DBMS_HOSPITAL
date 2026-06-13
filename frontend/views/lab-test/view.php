<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\TblLabTest $model */

$this->title = $model->test_id;
$this->params['breadcrumbs'][] = ['label' => 'Tbl Lab Tests', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="tbl-lab-test-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'test_id' => $model->test_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'test_id' => $model->test_id], [
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
            'test_id',
            'appt_id',
            'patient_id',
            'dr_id',
            'test_name',
            'test_category',
            'status',
            'results:ntext',
            'is_abnormal',
            'ordered_date',
            'results_date',
            'notes:ntext',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
