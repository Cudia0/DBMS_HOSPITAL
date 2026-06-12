<?php

use app\models\TblPrescription;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var app\models\PrescriptionSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Tbl Prescriptions';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-prescription-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Tbl Prescription', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'prescription_id',
            'appt_id',
            'med_id',
            'dr_id',
            'qty',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, TblPrescription $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'prescription_id' => $model->prescription_id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
