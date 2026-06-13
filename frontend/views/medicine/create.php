<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TblMedicine $model */

$this->title = 'Create Tbl Medicine';
$this->params['breadcrumbs'][] = ['label' => 'Tbl Medicines', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-medicine-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
