<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var object $model */

$this->title = $model->med_name;
$this->params['breadcrumbs'][] = ['label' => 'Medicines', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-medicine-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'med_id' => $model->med_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'med_id' => $model->med_id], [
            'class' => 'btn btn-danger',
            'data' => ['confirm' => 'Delete?', 'method' => 'post'],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'med_id',
            'med_name',
            'dosage_form',
            'strength',
            [
                'attribute' => 'med_price',
                'value' => $model->med_price ? '₱' . number_format($model->med_price, 2) : 'N/A',
            ],
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>