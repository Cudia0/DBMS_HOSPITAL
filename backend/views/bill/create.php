<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TblBill $model */

$this->title = 'Create Tbl Bill';
$this->params['breadcrumbs'][] = ['label' => 'Tbl Bills', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-bill-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
