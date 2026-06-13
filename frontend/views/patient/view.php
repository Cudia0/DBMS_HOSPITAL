<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var common\models\TblPatient $model */

$this->title = 'Patient: ' . $model->getFullName();
$this->params['breadcrumbs'][] = ['label' => 'Patients', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-patient-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'patient_id' => $model->patient_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'patient_id' => $model->patient_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this patient?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'patient_id',
            [
                'label' => 'Full Name',
                'value' => $model->getFullName(),
            ],
            'sex',
            [
                'label' => 'Age',
                'value' => $model->getAgeDisplay(),
            ],
            'date_of_birth:date',
            [
                'label' => 'Phone',
                'value' => ($model->country_code ? $model->country_code . ' ' : '') . $model->phone_num,
            ],
            'email:email',
            'address:ntext',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>