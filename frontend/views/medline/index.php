<?php

use common\models\TblMedline;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var app\models\MedlineSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Tbl Medlines';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-medline-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Tbl Medline', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'medline_id',
            'prescription_id',
            'med_id',
            'qty',
            'dosage_per_intake',
            'frequency',
            //'created_at',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, TblMedline $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'medline_id' => $model->medline_id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
