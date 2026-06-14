<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var common\models\BillSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'My Bills';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-bill-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-striped table-hover'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'bill_id',
            'appt_id',
            [
                'attribute' => 'total_amount',
                'label' => 'Total',
                'format' => 'raw',
                'value' => function($model) {
                    return '<strong>₱' . number_format($model->total_amount, 2) . '</strong>';
                },
                'contentOptions' => ['class' => 'text-end'],
            ],
            [
                'attribute' => 'payment_status',
                'format' => 'raw',
                'value' => function($model) {
                    $labels = [
                        'pending' => '<span class="badge bg-warning text-dark">Pending</span>',
                        'paid' => '<span class="badge bg-success">Paid</span>',
                    ];
                    return $labels[$model->payment_status] ?? $model->payment_status;
                },
            ],
            [
                'attribute' => 'bill_date',
                'format' => 'datetime',
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{view}',
                'urlCreator' => function ($action, $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'bill_id' => $model->bill_id]);
                },
            ],
        ],
    ]); ?>

</div>