<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var object $model */

$this->title = 'Medline #' . $model->medline_id;
$this->params['breadcrumbs'][] = ['label' => 'Medline', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-medline-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if (Yii::$app->user->identity->isDirector()): ?>
            <?= Html::a('Delete', ['delete', 'medline_id' => $model->medline_id], [
                'class' => 'btn btn-danger',
                'data' => ['confirm' => 'Delete?', 'method' => 'post'],
            ]) ?>
        <?php endif; ?>
        <?= Html::a('<i class="fas fa-arrow-left"></i> Back to List', ['index'], ['class' => 'btn btn-secondary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'medline_id',
            'prescription_id',
            'med_id',
            [
                'label' => 'Medicine',
                'value' => ($model->med_name ?? 'N/A') . (!empty($model->strength) ? ' (' . $model->strength . ')' : ''),
            ],
            'qty',
            'dosage_per_intake',
            'frequency',
            'created_at:datetime',
        ],
    ]) ?>

</div>