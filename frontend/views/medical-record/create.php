<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TblMedicalRecord $model */

$this->title = 'Create Tbl Medical Record';
$this->params['breadcrumbs'][] = ['label' => 'Tbl Medical Records', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-medical-record-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
