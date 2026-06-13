<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\TblPatient $model */

$this->title = $model->patient_id;
$this->params['breadcrumbs'][] = ['label' => 'Tbl Patients', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="tbl-patient-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'patient_id' => $model->patient_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'patient_id' => $model->patient_id], [
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
            'patient_id',
            'first_name',
            'middle_name',
            'last_name',
            'sex',
            'age',
            'date_of_birth',
            'phone_num',
            'country_code',
            'email:email',
            'address:ntext',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
