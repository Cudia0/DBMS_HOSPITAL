<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var object $model */

$fullName = ($model->first_name ?? '') . ' ' . ($model->last_name ?? '');
$this->title = 'Receptionist: ' . $fullName;
$this->params['breadcrumbs'][] = ['label' => 'Receptionists', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-receptionist-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'recep_id' => $model->recep_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'recep_id' => $model->recep_id], [
            'class' => 'btn btn-danger',
            'data' => ['confirm' => 'Delete this receptionist?', 'method' => 'post'],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'recep_id',
            'first_name',
            'middle_name',
            'last_name',
            'email:email',
            [
                'label' => 'Phone',
                'value' => ($model->country_code ? $model->country_code . ' ' : '') . ($model->phone_num ?? 'N/A'),
            ],
            [
                'label' => 'Director',
                'value' => ($model->director_fname ?? '') . ' ' . ($model->director_lname ?? 'N/A'),
            ],
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>