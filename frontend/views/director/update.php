<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\TblDirector $model */

$this->title = 'Update Tbl Director: ' . $model->director_id;
$this->params['breadcrumbs'][] = ['label' => 'Tbl Directors', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->director_id, 'url' => ['view', 'director_id' => $model->director_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="tbl-director-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
