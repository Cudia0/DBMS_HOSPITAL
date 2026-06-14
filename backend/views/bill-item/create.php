<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\TblBillItem $model */
/** @var common\models\TblBill $bill */

$this->title = 'Add Charge to Bill #' . ($bill->bill_id ?? '');
$this->params['breadcrumbs'][] = ['label' => 'Bills', 'url' => ['bill/index']];
if (isset($bill)) {
    $this->params['breadcrumbs'][] = ['label' => 'Bill #' . $bill->bill_id, 'url' => ['bill/view', 'bill_id' => $bill->bill_id]];
}
$this->params['breadcrumbs'][] = 'Add Item';
?>
<div class="tbl-bill-item-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'bill' => $bill ?? null,
    ]) ?>

</div>