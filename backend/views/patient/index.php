<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ArrayDataProvider $dataProvider */

$this->title = 'Patients';
$this->params['breadcrumbs'][] = $this->title;

$user = Yii::$app->user->identity;
$canCreate = $user && ($user->isDirector() || $user->isReceptionist());
?>
<div class="tbl-patient-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if ($canCreate): ?>
            <?= Html::a('<i class="fas fa-plus"></i> Add Patient', ['create'], ['class' => 'btn btn-success']) ?>
        <?php endif; ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-striped table-hover'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'patient_id',
                'label' => 'ID',
            ],
            [
                'label' => 'Full Name',
                'value' => function($model) {
                    return ($model['last_name'] ?? '') . ', ' . ($model['first_name'] ?? '');
                },
            ],
            [
                'attribute' => 'sex',
                'label' => 'Sex',
            ],
            [
                'attribute' => 'date_of_birth',
                'label' => 'Date of Birth',
                'format' => 'date',
            ],
            [
                'attribute' => 'email',
                'label' => 'Email',
            ],
            [
                'attribute' => 'phone_num',
                'label' => 'Phone',
            ],
            [
                'attribute' => 'created_at',
                'label' => 'Registered',
                'format' => 'datetime',
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{view} {update} {delete}',
                'visibleButtons' => [
                    'update' => function($model) use ($user) {
                        return $user && ($user->isDirector() || $user->isReceptionist());
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
                            'data' => ['confirm' => 'Delete this patient?', 'method' => 'post'],
                        ]);
                    },
                ],
                'urlCreator' => function ($action, $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'patient_id' => $model['patient_id']]);
                },
            ],
        ],
    ]); ?>

</div>