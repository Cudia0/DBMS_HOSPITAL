<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\helpers\Html;

$user = Yii::$app->user->identity;
$isGuest = Yii::$app->user->isGuest;
$role = $isGuest ? null : $user->role;
$fullName = $isGuest ? '' : $user->getFullName();
$roleLabel = $isGuest ? '' : $user->getRoleLabel();

$menuItems = [];

// HOME
$menuItems[] = ['label' => '<i class="fas fa-home"></i> Home', 'url' => ['/site/index']];

// PATIENT MENU
if ($role === 'patient') {
    $menuItems[] = ['label' => '<i class="fas fa-calendar-plus"></i> Book Appointment', 'url' => ['/appointment/create']];
    $menuItems[] = ['label' => '<i class="fas fa-calendar-check"></i> My Appointments', 'url' => ['/appointment/index']];
    $menuItems[] = ['label' => '<i class="fas fa-notes-medical"></i> My Medical Records', 'url' => ['/medical-record/index']];
    $menuItems[] = ['label' => '<i class="fas fa-prescription"></i> My Prescriptions', 'url' => ['/prescription/index']];
    $menuItems[] = ['label' => '<i class="fas fa-file-invoice-dollar"></i> My Bills', 'url' => ['/bill/index']];
    
}

// About & Contact - Always visible
$menuItems[] = ['label' => '<i class="fas fa-info-circle"></i> About', 'url' => ['/site/about']];
$menuItems[] = ['label' => '<i class="fas fa-envelope"></i> Contact', 'url' => ['/site/contact']];

// USER ACCOUNT
if ($isGuest) {
    $menuItems[] = ['label' => '<i class="fas fa-user-plus"></i> Register', 'url' => ['/site/signup']];
    $menuItems[] = ['label' => '<i class="fas fa-sign-in-alt"></i> Login', 'url' => ['/site/login']];
} else {
    $menuItems[] = [
        'label' => '<i class="fas fa-user-circle"></i> ' . Html::encode($fullName),
        'items' => [
            ['label' => '<i class="fas fa-cog"></i> Profile Settings', 'url' => ['/profile/index']],
            ['label' => '<i class="fas fa-sign-out-alt"></i> Logout (' . Html::encode($user->username) . ')', 'url' => ['/site/logout'], 'linkOptions' => ['data-method' => 'post']],
        ],
    ];
}

?>
<header id="header">
    <?php NavBar::begin([
        'brandLabel' => Html::img('@web/images/medisync-logo-dark.svg', ['alt' => 'MediSync', 'height' => 40, 'class' => 'd-inline-block align-top me-2']) . ' MediSync',
        'brandUrl' => ' ',
        'options' => ['class' => 'navbar-expand-lg navbar-dark bg-dark fixed-top shadow', 'id' => 'main-navbar'],
        'innerContainerOptions' => ['class' => 'container-fluid'],
    ]); ?>
    
    <?= Nav::widget([
        'options' => ['class' => 'navbar-nav ms-auto'],
        'encodeLabels' => false,
        'items' => $menuItems,
    ]) ?>
    
    <button id="theme-toggle" class="btn btn-link nav-link ms-2 fs-5" aria-label="Toggle dark mode" title="Toggle dark/light mode" style="border: none; background: transparent;">
        <i class="fas fa-moon" id="theme-icon"></i>
    </button>
    
    <?php NavBar::end() ?>
</header>
<div style="padding-top: 70px;"></div>