<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ArrayDataProvider $dataProvider */

$this->title = 'Bills';
$this->params['breadcrumbs'][] = $this->title;

$user = Yii::$app->user->identity;
$isReceptionist = $user && $user->isReceptionist();
$isDirector = $user && $user->isDirector();
?>
<div class="tbl-bill-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if ($isDirector): ?>
    <p>
        <?= Html::a('<i class="fas fa-plus"></i> Create Bill', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php endif; ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-striped table-hover'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'bill_id',
            'appt_id',
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
                'attribute' => 'total_amount',
                'label' => 'Total',
                'format' => 'raw',
                'value' => function($model) {
                    return '<strong>₱' . number_format($model['total_amount'], 2) . '</strong>';
                },
                'contentOptions' => ['class' => 'text-end'],
            ],
            [
                'attribute' => 'payment_status',
                'label' => 'Status',
                'format' => 'raw',
                'value' => function($model) {
                    $labels = [
                        'pending' => '<span class="badge bg-warning text-dark">Pending</span>',
                        'partial' => '<span class="badge bg-info">Partial</span>',
                        'paid' => '<span class="badge bg-success">Paid</span>',
                        'refunded' => '<span class="badge bg-secondary">Refunded</span>',
                        'cancelled' => '<span class="badge bg-danger">Cancelled</span>',
                    ];
                    return $labels[$model['payment_status']] ?? $model['payment_status'];
                },
            ],
            'payment_method',
            [
                'attribute' => 'bill_date',
                'label' => 'Date',
                'format' => 'datetime',
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{view} {print} {update} {delete} {pay}',
                'visibleButtons' => [
                    'print' => function($model) use ($isReceptionist, $isDirector) {
                        return $isReceptionist || $isDirector;
                    },
                    'update' => function($model) use ($isDirector) {
                        return $isDirector;
                    },
                    'delete' => function($model) use ($isDirector) {
                        return $isDirector;
                    },
                    'pay' => function($model) use ($isReceptionist, $isDirector) {
                        return ($isReceptionist || $isDirector) && ($model['payment_status'] === 'pending' || $model['payment_status'] === 'partial');
                    },
                ],
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::a('<i class="fas fa-eye"></i>', $url, ['title' => 'View', 'class' => 'btn btn-primary btn-sm']);
                    },
                    'print' => function ($url, $model) {
                        return Html::a('<i class="fas fa-print"></i>', $url, ['title' => 'Print', 'class' => 'btn btn-secondary btn-sm', 'target' => '_blank']);
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
                    'pay' => function ($url, $model) {
                        return Html::a('<i class="fas fa-check-circle"></i>', ['view', 'bill_id' => $model['bill_id']], ['title' => 'Process Payment', 'class' => 'btn btn-success btn-sm']);
                    },
                ],
                'urlCreator' => function ($action, $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'bill_id' => $model['bill_id']]);
                },
            ],
        ],
    ]); ?>

</div>