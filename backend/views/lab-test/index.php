<?php

use common\models\TblLabTest;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var app\models\LabTestSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Tbl Lab Tests';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-lab-test-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Tbl Lab Test', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'test_id',
            'appt_id',
            'patient_id',
            'dr_id',
            'test_name',
            'test_category',
            'status',
            'results:ntext',
            'is_abnormal',
            'ordered_date',
            'results_date',
            'notes:ntext',
            //'created_at',
            //'updated_at',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, TblLabTest $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'test_id' => $model->test_id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
