<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TblMedline $model */

$this->title = 'Create Tbl Medline';
$this->params['breadcrumbs'][] = ['label' => 'Tbl Medlines', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-medline-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
