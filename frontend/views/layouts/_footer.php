<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use yii\helpers\Html;

?>
<footer id="footer" class="mt-auto py-3 bg-body-tertiary">
    <div class="container">
        <div class="row text-body-secondary">
            <div class="col-md-6 text-center text-md-start">&copy; <?= Html::encode(Yii::$app->name) ?> <?= date('Y') ?></div>
            <div class="col-md-6 text-center text-md-end">
                
                    <?= Html::img('@web/images/hospital_management_system.svg', [
    'alt' => 'MediSync',
    'height' => 50,
    'class' => 'logo'
]) ?>
            </div>
        </div>
    </div>
</footer>
