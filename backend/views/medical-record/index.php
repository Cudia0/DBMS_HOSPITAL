<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ArrayDataProvider $dataProvider */

$this->title = 'Medical Records';
$this->params['breadcrumbs'][] = $this->title;

$user = Yii::$app->user->identity;
$canCreate = $user && ($user->isDirector() || $user->isDoctor());
?>
<div class="tbl-medical-record-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if ($canCreate): ?>
            <?= Html::a('<i class="fas fa-plus"></i> Create Medical Record', ['create'], ['class' => 'btn btn-success']) ?>
        <?php endif; ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-striped table-hover'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'record_id',
                'label' => 'ID',
            ],
            [
                'attribute' => 'appt_id',
                'label' => 'Appt',
            ],
            [
                'label' => 'Patient',
                'value' => function($model) {
                    return ($model['patient_fname'] ?? '') . ' ' . ($model['patient_lname'] ?? 'N/A');
                },
            ],
            [
                'label' => 'Doctor',
                'value' => function($model) {
                    return isset($model['doctor_lname']) ? 'Dr. ' . $model['doctor_lname'] : 'N/A';
                },
            ],
            [
                'attribute' => 'vital_signs',
                'label' => 'Vital Signs',
                'value' => function($model) {
                    return !empty($model['vital_signs']) ? substr($model['vital_signs'], 0, 30) . '...' : '<span class="text-muted">Not recorded</span>';
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'diagnosis',
                'label' => 'Diagnosis',
                'value' => function($model) {
                    return !empty($model['diagnosis']) ? substr($model['diagnosis'], 0, 50) . '...' : '<span class="text-muted">No diagnosis</span>';
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'treatment_plan',
                'label' => 'Treatment',
                'value' => function($model) {
                    return !empty($model['treatment_plan']) ? substr($model['treatment_plan'], 0, 30) . '...' : '<span class="text-muted">No plan</span>';
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'notes',
                'label' => 'Notes',
                'value' => function($model) {
                    return !empty($model['notes']) ? substr($model['notes'], 0, 30) . '...' : '<span class="text-muted">None</span>';
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'record_date',
                'label' => 'Date',
                'format' => 'datetime',
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{view} {update} {delete}',
                'visibleButtons' => [
                    'update' => function($model) use ($user) {
                        return $user && ($user->isDirector() || $user->isDoctor());
                    },
                    'delete' => function($model) use ($user) {
                        return $user && $user->isDirector();
                    },
                ],
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('<i class="fas fa-eye"></i>', $url, ['title' => 'View', 'class' => 'btn btn-primary btn-sm']);
                    },
                    'update' => function ($url, $model) {
                        return Html::a('<i class="fas fa-edit"></i>', $url, ['title' => 'Edit', 'class' => 'btn btn-info btn-sm']);
                    },
                    'delete' => function ($url, $model) {
                        return Html::a('<i class="fas fa-trash"></i>', $url, [
                            'title' => 'Delete', 'class' => 'btn btn-danger btn-sm',
                            'data' => ['confirm' => 'Delete?', 'method' => 'post'],
                        ]);
                    },
                ],
                'urlCreator' => function ($action, $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'record_id' => $model['record_id']]);
                },
            ],
        ],
    ]); ?>

</div>