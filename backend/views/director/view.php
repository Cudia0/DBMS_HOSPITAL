<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\TblDirector $model */

$this->title = $model->director_id;
$this->params['breadcrumbs'][] = ['label' => 'Tbl Directors', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="tbl-director-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'director_id' => $model->director_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'director_id' => $model->director_id], [
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
            'director_id',
            'full_name',
            'phone_num',
            'country_code',
            'email:email',
            'recep_id',
        ],
    ]) ?>

</div>
