<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ArrayDataProvider $dataProvider */

$this->title = 'Prescriptions';
$this->params['breadcrumbs'][] = $this->title;

$user = Yii::$app->user->identity;
$canCreate = $user && ($user->isDirector() || $user->isDoctor());
?>
<div class="tbl-prescription-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if ($canCreate): ?>
            <?= Html::a('<i class="fas fa-plus"></i> Create Prescription', ['create'], ['class' => 'btn btn-success']) ?>
        <?php endif; ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-striped table-hover'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'prescription_id',
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
                'attribute' => 'prescription_date',
                'label' => 'Date',
                'format' => 'datetime',
            ],
            [
                'attribute' => 'duration_days',
                'label' => 'Duration',
                'value' => function($model) {
                    return $model['duration_days'] ? $model['duration_days'] . ' days' : 'N/A';
                },
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
                    return Url::toRoute([$action, 'prescription_id' => $model['prescription_id']]);
                },
            ],
        ],
    ]); ?>

</div>