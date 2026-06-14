<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var object $model */

$fullName = ($model->first_name ?? '') . ' ' . ($model->last_name ?? '');
$this->title = 'Director: ' . $fullName;
$this->params['breadcrumbs'][] = ['label' => 'Directors', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-director-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'director_id' => $model->director_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'director_id' => $model->director_id], [
            'class' => 'btn btn-danger',
            'data' => ['confirm' => 'Delete this director?', 'method' => 'post'],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'director_id',
            'first_name',
            'middle_name',
            'last_name',
            [
                'label' => 'Phone',
                'value' => ($model->country_code ? $model->country_code . ' ' : '') . ($model->phone_num ?? 'N/A'),
            ],
            'email:email',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>