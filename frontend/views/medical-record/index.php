<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\models\TblMedicalRecord;

/** @var yii\web\View $this */
/** @var common\models\MedicalRecordSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Medical Records';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-medical-record-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Medical Record', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'record_id',
            [
                'attribute' => 'appt_id',
                'label' => 'Appointment',
                'value' => function ($model) {
                    return $model->appointment ? 'Appt #' . $model->appt_id . ' - ' . $model->appointment->appointment_date : 'N/A';
                }
            ],
            [
                'label' => 'Patient',
                'value' => function ($model) {
                    return $model->patient ? $model->patient->getFullName() : 'N/A';
                }
            ],
            [
                'label' => 'Doctor',
                'value' => function ($model) {
                    return $model->doctor ? $model->doctor->getFullName() : 'N/A';
                }
            ],
            'diagnosis:ntext',
            'vital_signs',
            'record_date',
            [
                'class' => ActionColumn::className(),
                'urlCreator' => function ($action, TblMedicalRecord $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'record_id' => $model->record_id]);
                 }
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>

</div>