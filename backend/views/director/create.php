<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TblDirector $model */

$this->title = 'Create Tbl Director';
$this->params['breadcrumbs'][] = ['label' => 'Tbl Directors', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tbl-director-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
