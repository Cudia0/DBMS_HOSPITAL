<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\TblBill $model */

$this->title = 'Update Bill #' . $model->bill_id;
$this->params['breadcrumbs'][] = ['label' => 'Bills', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => 'Bill #' . $model->bill_id, 'url' => ['view', 'bill_id' => $model->bill_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tbl-bill-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>