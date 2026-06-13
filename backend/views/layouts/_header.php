<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\helpers\Html;

$items = [
    [
        'label' => 'Home',
        'url' => ['/site/index'],
     ],
     [
        'label' => 'Departments',
        'url' => ['/department/index'],
    ],[
        'label' => 'Directors',
        'url' => ['/director/index'],
    ],[
        'label' => 'Medicines',
        'url' => ['/medicine/index'],
    ],[
        'label' => 'Receptionists',
        'url' => ['/receptionist/index'],
    ],[
        'label' => 'Doctor',
        'url' => ['/doctor/index'],
    ],[
        'label' => 'Patients',
        'url' => ['/patient/index'],
    ],[
        'label' => 'Appointments',
        'url' => ['/appointment/index'],
    ],[
        'label' => 'Medical Records',
        'url' => ['/medical-record/index'],
    ],[
        'label' => 'Prescriptions',
        'url' => ['/prescription/index'],
    ],[
        'label' => 'Medical Line',
        'url' => ['/medline/index'],
    ],[
        'label' => 'Lab Tests',
        'url' => ['/lab-test/index'],
    ],[
        'label' => 'Bills',
        'url' => ['/bill/index'],
    ],[
        'label' => 'Bill Items',
        'url' => ['/bill-item/index'],
    ],
    
    // [
    //     'label' => 'About',
    //     'url' => ['/site/about'],
    // ],
    // [
    //     'label' => 'Contact',
    //     'url' => ['/site/contact'],
    // ],
     [
         'label' => 'Signup',
         'url' => ['/site/signup'],
         'visible' => Yii::$app->user->isGuest,
     ],
     [
         'label' => 'Login',
         'url' => ['/site/login'],
         'visible' => Yii::$app->user->isGuest,
     ],
    [
        'label' => 'Logout (' . Html::encode(Yii::$app->user->identity?->username) . ')',
        'url' => ['/site/logout'],
        'linkOptions' => [
            'data-method' => 'post',
            'class' => 'logout',
        ],
        'visible' => !Yii::$app->user->isGuest,
    ],
];

?>
<header id="header">
    <?php NavBar::begin(
        [
            'brandLabel' => Yii::$app->name,
            'brandUrl' => Yii::$app->homeUrl,
            'options' => ['class' => 'navbar-expand-md navbar-dark bg-dark fixed-top']
        ],
    ) ?>
    <?= Nav::widget(
        [
            'options' => ['class' => 'navbar-nav me-auto'],
            'encodeLabels' => false,
            'items' => $items,
        ],
    ) ?>
    <?= Html::button(
        '&#127769;',
        [
            'id' => 'theme-toggle',
            'class' => 'btn btn-link nav-link fs-5',
            'aria-label' => 'Switch to dark mode',
        ],
    ) ?>
    <?php NavBar::end() ?>
</header>
