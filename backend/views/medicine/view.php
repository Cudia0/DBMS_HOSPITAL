<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\TblMedicine $model */

$this->title = $model->med_id;
$this->params['breadcrumbs'][] = ['label' => 'Tbl Medicines', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="tbl-medicine-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'med_id' => $model->med_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'med_id' => $model->med_id], [
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
            'med_id',
            'med_name',
            'med_price',
        ],
    ]) ?>

</div>
