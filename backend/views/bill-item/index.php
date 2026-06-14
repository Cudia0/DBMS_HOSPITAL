<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use common\models\TblBillItem;
use common\models\TblBill;

/** @var yii\web\View $this */
/** @var common\models\BillItemSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Bill Items';
$this->params['breadcrumbs'][] = $this->title;

$user = Yii::$app->user->identity;
$isReceptionist = $user && $user->isReceptionist();
$isDirector = $user && $user->isDirector();
?>
<div class="tbl-bill-item-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fas fa-list"></i> View All Bills', ['bill/index'], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-striped table-hover'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            
            'bill_item_id',
            [
                'attribute' => 'bill_id',
                'label' => 'Bill',
                'format' => 'raw',
                'value' => function($model) {
                    $bill = TblBill::findOne($model->bill_id);
                    $status = $bill ? $bill->payment_status : 'unknown';
                    $statusColors = ['pending' => 'warning', 'paid' => 'success'];
                    $color = $statusColors[$status] ?? 'secondary';
                    return Html::a('Bill #' . $model->bill_id, ['bill/view', 'bill_id' => $model->bill_id]) . 
                           ' <span class="badge bg-' . $color . '">' . ucfirst($status) . '</span>';
                },
            ],
            [
                'attribute' => 'item_type',
                'format' => 'raw',
                'value' => function($model) {
                    $labels = [
                        'consultation' => '<span class="badge bg-primary">Consultation</span>',
                        'medicine' => '<span class="badge bg-success">Medicine</span>',
                        'lab_test' => '<span class="badge bg-warning text-dark">Lab Test</span>',
                        'procedure' => '<span class="badge bg-info">Procedure</span>',
                        'other' => '<span class="badge bg-secondary">Other</span>',
                    ];
                    return $labels[$model->item_type] ?? $model->item_type;
                },
                'filter' => [
                    'consultation' => 'Consultation',
                    'medicine' => 'Medicine',
                    'lab_test' => 'Lab Test',
                    'procedure' => 'Procedure',
                    'other' => 'Other',
                ],
            ],
            'description',
            'quantity',
            [
                'attribute' => 'unit_price',
                'value' => function($model) {
                    return '₱' . number_format($model->unit_price, 2);
                },
            ],
            [
                'attribute' => 'total_price',
                'format' => 'raw',
                'value' => function($model) {
                    return '<strong>₱' . number_format($model->total_price, 2) . '</strong>';
                },
            ],
            'created_at:datetime',
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
                'urlCreator' => function ($action, $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'bill_item_id' => $model->bill_item_id]);
                },
            ],
        ],
    ]); ?>

</div>