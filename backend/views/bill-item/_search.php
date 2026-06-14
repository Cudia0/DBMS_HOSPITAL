<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\TblBillItem $model */
/** @var array|null $bill */

$billId = is_array($bill) ? ($bill['bill_id'] ?? '') : ($bill->bill_id ?? '');
$this->title = 'Add Charge to Bill #' . $billId;
$this->params['breadcrumbs'][] = ['label' => 'Bills', 'url' => ['bill/index']];
if ($bill) {
    $this->params['breadcrumbs'][] = ['label' => 'Bill #' . $billId, 'url' => ['bill/view', 'bill_id' => $billId]];
}
$this->params['breadcrumbs'][] = 'Add Item';
?>
<div class="tbl-bill-item-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'bill' => $bill,
    ]) ?>

</div>