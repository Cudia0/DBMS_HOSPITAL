<?php

use common\models\TblScheduleSlot;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var app\models\ScheduleSlotSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Tbl Schedule Slots';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-schedule-slot-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Tbl Schedule Slot', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'slot_id',
            'dr_id',
            'slot_date',
            'start_time',
            'end_time',
            //'is_available',
            //'created_at',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, TblScheduleSlot $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'slot_id' => $model->slot_id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
