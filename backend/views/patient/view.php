<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var object $model */

$fullName = ($model->last_name ?? '') . ', ' . ($model->first_name ?? '');
$this->title = 'Patient: ' . $fullName;
$this->params['breadcrumbs'][] = ['label' => 'Patients', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$user = Yii::$app->user->identity;
$canEdit = $user && ($user->isDirector() || $user->isReceptionist());
?>
<div class="tbl-patient-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if ($canEdit): ?>
            <?= Html::a('Update', ['update', 'patient_id' => $model->patient_id], ['class' => 'btn btn-primary']) ?>
        <?php endif; ?>
        <?php if ($user && $user->isDirector()): ?>
            <?= Html::a('Delete', ['delete', 'patient_id' => $model->patient_id], [
                'class' => 'btn btn-danger',
                'data' => ['confirm' => 'Delete this patient?', 'method' => 'post'],
            ]) ?>
        <?php endif; ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'patient_id',
            [
                'label' => 'Full Name',
                'value' => $fullName,
            ],
            [
                'label' => 'First Name',
                'attribute' => 'first_name',
            ],
            [
                'label' => 'Middle Name',
                'attribute' => 'middle_name',
                'value' => $model->middle_name ?? 'N/A',
            ],
            [
                'label' => 'Last Name',
                'attribute' => 'last_name',
            ],
            'sex',
            [
                'label' => 'Age',
                'value' => function($model) {
                    if (!empty($model->date_of_birth)) {
                        $dob = new DateTime($model->date_of_birth);
                        $now = new DateTime();
                        return $now->diff($dob)->y . ' years old';
                    }
                    return 'N/A';
                },
            ],
            'date_of_birth:date',
            [
                'label' => 'Phone',
                'value' => ($model->country_code ? $model->country_code . ' ' : '') . ($model->phone_num ?? 'N/A'),
            ],
            'email:email',
            'address:ntext',
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>