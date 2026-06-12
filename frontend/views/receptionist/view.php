<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\TblReceptionist $model */

$this->title = $model->recep_id;
$this->params['breadcrumbs'][] = ['label' => 'Tbl Receptionists', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="tbl-receptionist-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'recep_id' => $model->recep_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'recep_id' => $model->recep_id], [
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
            'recep_id',
            'Full_Name',
            'Email:email',
            'phone_num',
            'country_code',
        ],
    ]) ?>

</div>
