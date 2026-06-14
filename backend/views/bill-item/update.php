<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\TblBillItem $model */
/** @var array|null $bill */

$billId = '';
if ($bill) {
    $billId = is_array($bill) ? ($bill['bill_id'] ?? '') : ($bill->bill_id ?? '');
}
$this->title = 'Update Charge #' . $model->bill_item_id;
$this->params['breadcrumbs'][] = ['label' => 'Bills', 'url' => ['bill/index']];
if ($bill && $billId) {
    $this->params['breadcrumbs'][] = ['label' => 'Bill #' . $billId, 'url' => ['bill/view', 'bill_id' => $billId]];
}
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tbl-bill-item-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'bill' => $bill,
    ]) ?>

</div>