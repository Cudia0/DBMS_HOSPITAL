<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\TblBill $model */

$this->title = 'Create Bill';
$this->params['breadcrumbs'][] = ['label' => 'Bills', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-bill-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>