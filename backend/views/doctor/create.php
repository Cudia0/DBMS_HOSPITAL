<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TblDoctor $model */

$this->title = 'Create Tbl Doctor';
$this->params['breadcrumbs'][] = ['label' => 'Tbl Doctors', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-doctor-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
