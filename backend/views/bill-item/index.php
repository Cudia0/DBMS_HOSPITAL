<?php

use common\models\TblBillItem;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var app\models\BillItemSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Tbl Bill Items';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-bill-item-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Tbl Bill Item', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'bill_item_id',
            'bill_id',
            'item_type',
            'description',
            'reference_id',
            'quantity',
            'unit_price',
            'total_price',
            //'created_at',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, TblBillItem $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'bill_item_id' => $model->bill_item_id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
