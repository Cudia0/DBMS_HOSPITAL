<?php

use yii\helpers\Html;
use common\models\TblMedline;

/** @var yii\web\View $this */
/** @var common\models\TblPrescription $model */

$this->title = 'Prescription #' . $model->prescription_id;
$this->params['breadcrumbs'][] = ['label' => 'My Prescriptions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$medlines = TblMedline::find()->where(['prescription_id' => $model->prescription_id])->all();
?>
<div class="tbl-prescription-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="card mb-4">
        <div class="card-header bg-dark text-white"><h5 class="mb-0"><i class="fas fa-info-circle"></i> Prescription Details</h5></div>
        <div class="card-body">
            <table class="table table-borderless mb-0">
                <tr><th width="150">Prescription #:</th><td><?= $model->prescription_id ?></td></tr>
                <tr><th>Date:</th><td><?= Yii::$app->formatter->asDatetime($model->prescription_date, 'medium') ?></td></tr>
                <?php if ($model->doctor): ?>
                <tr><th>Doctor:</th><td>Dr. <?= Html::encode($model->doctor->first_name . ' ' . $model->doctor->last_name) ?></td></tr>
                <?php endif; ?>
                <tr><th>Duration:</th><td><?= $model->duration_days ? $model->duration_days . ' days' : 'N/A' ?></td></tr>
            </table>
        </div>
    </div>

    <?php if ($model->dosage_instructions): ?>
    <div class="card mb-4">
        <div class="card-header bg-info text-white"><h5 class="mb-0"><i class="fas fa-pills"></i> Instructions</h5></div>
        <div class="card-body"><div class="p-3 bg-light rounded"><?= nl2br(Html::encode($model->dosage_instructions)) ?></div></div>
    </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header bg-success text-white"><h5 class="mb-0"><i class="fas fa-capsules"></i> Medicines</h5></div>
        <div class="card-body">
            <?php if (!empty($medlines)): ?>
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr><th>Medicine</th><th>Qty</th><th>Dosage</th><th>Frequency</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($medlines as $medline): ?>
                    <tr>
                        <td><?= $medline->medicine ? Html::encode($medline->medicine->med_name . ' (' . $medline->medicine->strength . ')') : 'N/A' ?></td>
                        <td><?= $medline->qty ?></td>
                        <td><?= Html::encode($medline->dosage_per_intake ?? '-') ?></td>
                        <td><?= Html::encode($medline->frequency ?? '-') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <p class="text-muted">No medicines prescribed.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($model->notes): ?>
    <div class="card mt-4">
        <div class="card-header bg-secondary text-white"><h5 class="mb-0"><i class="fas fa-sticky-note"></i> Notes</h5></div>
        <div class="card-body"><div class="p-3 bg-light rounded"><?= nl2br(Html::encode($model->notes)) ?></div></div>
    </div>
    <?php endif; ?>

</div>