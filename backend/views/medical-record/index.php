<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use common\models\TblMedicalRecord;

/** @var yii\web\View $this */
/** @var common\models\MedicalRecordSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Medical Records';
$this->params['breadcrumbs'][] = $this->title;

$user = Yii::$app->user->identity;
$canCreate = $user && ($user->isDirector() || $user->isDoctor());
?>
<div class="tbl-medical-record-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if ($canCreate): ?>
            <?= Html::a('<i class="fas fa-plus"></i> Create Medical Record', ['create'], ['class' => 'btn btn-success']) ?>
        <?php endif; ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-striped table-hover'],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            
            'record_id',
            'appt_id',
            [
                'attribute' => 'vital_signs',
                'label' => 'Vital Signs',
                'value' => function($model) {
                    return $model->vital_signs ? substr($model->vital_signs, 0, 30) . '...' : '<span class="text-muted">Not recorded</span>';
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'diagnosis',
                'value' => function($model) {
                    return $model->diagnosis ? substr($model->diagnosis, 0, 40) . '...' : '<span class="text-muted">No diagnosis</span>';
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'treatment_plan',
                'label' => 'Treatment',
                'value' => function($model) {
                    return $model->treatment_plan ? substr($model->treatment_plan, 0, 30) . '...' : '<span class="text-muted">No plan</span>';
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'notes',
                'value' => function($model) {
                    return $model->notes ? substr($model->notes, 0, 30) . '...' : '<span class="text-muted">None</span>';
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'record_date',
                'format' => 'datetime',
                'label' => 'Date',
            ],
            [
                'class' => ActionColumn::class,
                'template' => '{view} {update} {delete}',
                'visibleButtons' => [
                    'update' => function($model) use ($user) {
                        return $user && ($user->isDirector() || $user->isDoctor());
                    },
                    'delete' => function($model) use ($user) {
                        return $user && $user->isDirector();
                    },
                ],
                'urlCreator' => function ($action, $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'record_id' => $model->record_id]);
                },
            ],
        ],
    ]); ?>

</div>