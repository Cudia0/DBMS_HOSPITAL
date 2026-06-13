<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TblLabTest $model */

$this->title = 'Create Tbl Lab Test';
$this->params['breadcrumbs'][] = ['label' => 'Tbl Lab Tests', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-lab-test-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
