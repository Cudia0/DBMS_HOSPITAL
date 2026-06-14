<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use yii\helpers\Html;

?>
<footer id="footer" class="bg-dark text-light py-1 mt-auto">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-4 text-center text-md-start mb-3 mb-md-0">
                <span class="small">&copy; <?= Html::encode(Yii::$app->name) ?> <?= date('Y') ?></span><br>
                <span class="small opacity-75">Hospital Management System</span>
            </div>
            <div class="col-md-4 text-center mb-3 mb-md-0">
                <?= Html::img('@web/images/medisync-logo-white.svg', [
                    'alt' => 'MediSync',
                    'height' => 40,
                    'class' => 'footer-logo'
                ]) ?>
            </div>
            <div class="col-md-4 text-center text-md-end">
                <span class="small opacity-75">Powered by Yii2 Framework</span>
            </div>
        </div>
    </div>
</footer>