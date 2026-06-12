<?php

use app\models\TblReceptionist;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/** @var app\models\ReceptionistSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Tbl Receptionists';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-receptionist-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Tbl Receptionist', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'recep_id',
            'Full_Name',
            'Email:email',
            'phone_num',
            'country_code',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, TblReceptionist $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'recep_id' => $model->recep_id]);
                 }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
