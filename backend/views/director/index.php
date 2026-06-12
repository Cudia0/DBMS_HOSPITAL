<?php

use app\models\TblDirector;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\DirectorSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Tbl Directors';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-director-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Tbl Director', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'director_id',
            'full_name',
            'phone_num',
            'country_code',
            'email:email',
            'recep_id',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, TblDirector $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'director_id' => $model->director_id]);
                 }
            ],
        ],
    ]); ?>


</div>
