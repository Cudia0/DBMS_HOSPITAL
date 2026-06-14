<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ArrayDataProvider $dataProvider */

$this->title = 'Medline (Prescription Items)';
$this->params['breadcrumbs'][] = $this->title;

$user = Yii::$app->user->identity;
$isDirector = $user && $user->isDirector();
?>
<div class="tbl-medline-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if ($isDirector): ?>
    <p>
        <?= Html::a('<i class="fas fa-plus"></i> Add Medline', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php endif; ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-striped table-hover'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'medline_id',
            'prescription_id',
            'med_id',
            [
                'label' => 'Medicine',
                'value' => function($model) {
                    return ($model['med_name'] ?? 'N/A') . ($model['strength'] ? ' (' . $model['strength'] . ')' : '');
                },
            ],
            'qty',
            'dosage_per_intake',
            'frequency',
            [
                'class' => ActionColumn::class,
                'template' => '{view} {delete}',
                'visibleButtons' => [
                    'delete' => function($model) use ($isDirector) {
                        return $isDirector;
                    },
                ],
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('<i class="fas fa-eye"></i>', $url, ['title' => 'View', 'class' => 'btn btn-primary btn-sm']);
                    },
                    'delete' => function ($url, $model) {
                        return Html::a('<i class="fas fa-trash"></i>', $url, [
                            'title' => 'Delete', 'class' => 'btn btn-danger btn-sm',
                            'data' => ['confirm' => 'Delete?', 'method' => 'post'],
                        ]);
                    },
                ],
                'urlCreator' => function ($action, $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'medline_id' => $model['medline_id']]);
                },
            ],
        ],
    ]); ?>

</div>