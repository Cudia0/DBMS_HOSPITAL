<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TblReceptionist $model */

$this->title = 'Create Tbl Receptionist';
$this->params['breadcrumbs'][] = ['label' => 'Tbl Receptionists', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-receptionist-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
