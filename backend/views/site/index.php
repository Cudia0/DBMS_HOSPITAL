<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use yii\helpers\Html;

$this->title = 'medisync';
$this->params['meta_description'] = 'A high-performance PHP framework best for developing web applications. Fast, secure, and professional.';
$this->params['meta_keywords'] = 'yii, yii2, php, framework, web application, high-performance';
?>
<div class="site-index">

    <!-- Hero banner with Yii gradient -->
    <div class="hero-banner text-white rounded-4 p-5 mb-4 position-relative overflow-hidden">
        <?= Html::img(Yii::getAlias(''), [
            'alt' => '',
            'class' => 'd-none d-lg-block position-absolute hero-logo',
        ]) ?>
        <div class="position-relative">
            <h1 class="display-5 fw-bold mb-3">Rorrr</h1>
            <p class="lead opacity-75 mb-4 hero-lead">
                A high-performance PHP framework best for developing web applications.
                Fast, secure, and professional.
            </p>
            <div class="d-flex gap-2 flex-wrap">
                <?= Html::a(
                    'Click meeee!!!!',
                    'https://youtu.be/Aq5WXmQQooo',
                    [
                        'class' => 'btn btn-light btn-lg fw-semibold px-4',
                        'rel' => 'noopener',
                        'target' => '_blank',
                    ],
                ) ?>
            </div>
        </div>
    </div>
</div>
