<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\helpers\Html;

// Get current user and role
$user = Yii::$app->user->identity;
$isGuest = Yii::$app->user->isGuest;
$role = $isGuest ? null : $user->role;
$fullName = $isGuest ? '' : $user->getFullName();
$roleLabel = $isGuest ? '' : $user->getRoleLabel();

// Define menu items based on role
$menuItems = [];

// HOME - Available to all
$menuItems[] = [
    'label' => '<i class="fas fa-home"></i> Home',
    'url' => ['/site/index'],
];

// ============================================
// DIRECTOR MENU (Full Access)
// ============================================
if ($role === 'director') {
    $menuItems[] = [
    'label' => '<i class="fas fa-shield-alt"></i> System',
    'items' => [
        ['label' => '<i class="fas fa-users-cog"></i> User Management', 'url' => ['/user/index']],
        ['label' => '<i class="fas fa-cogs"></i> System Configuration', 'url' => ['/settings/index']],
    ],
];
    $menuItems[] = [
        'label' => '<i class="fas fa-cog"></i> Master Data',
        'items' => [
            ['label' => '<i class="fas fa-building"></i> Departments', 'url' => ['/department/index']],
            ['label' => '<i class="fas fa-user-tie"></i> Directors', 'url' => ['/director/index']],
            ['label' => '<i class="fas fa-pills"></i> Medicines', 'url' => ['/medicine/index']],
        ],
    ];
    $menuItems[] = [
        'label' => '<i class="fas fa-users"></i> Staff',
        'items' => [
            ['label' => '<i class="fas fa-user-md"></i> Doctors', 'url' => ['/doctor/index']],
            ['label' => '<i class="fas fa-user"></i> Receptionists', 'url' => ['/receptionist/index']],
        ],
    ];
    $menuItems[] = [
        'label' => '<i class="fas fa-procedures"></i> Patients',
        'url' => ['/patient/index'],
    ];
    $menuItems[] = [
        'label' => '<i class="fas fa-calendar-alt"></i> Appointments',
        'url' => ['/appointment/index'],
    ];
    $menuItems[] = [
        'label' => '<i class="fas fa-clipboard-list"></i> Clinical',
        'items' => [
            ['label' => '<i class="fas fa-notes-medical"></i> Medical Records', 'url' => ['/medical-record/index']],
            ['label' => '<i class="fas fa-prescription"></i> Prescriptions', 'url' => ['/prescription/index']],
            ['label' => '<i class="fas fa-flask"></i> Lab Tests', 'url' => ['/lab-test/index']],
            ['label' => '<i class="fas fa-tablets"></i> Medline', 'url' => ['/medline/index']],
        ],
    ];
    $menuItems[] = [
        'label' => '<i class="fas fa-file-invoice"></i> Billing',
        'items' => [
            ['label' => '<i class="fas fa-file-invoice-dollar"></i> Bills', 'url' => ['/bill/index']],
            ['label' => '<i class="fas fa-list-alt"></i> Bill Items', 'url' => ['/bill-item/index']],
        ],
    ];
}

// ============================================
// RECEPTIONIST MENU
// ============================================
elseif ($role === 'receptionist') {
    $menuItems[] = [
        'label' => '<i class="fas fa-procedures"></i> Patients',
        'url' => ['/patient/index'],
    ];
    $menuItems[] = [
        'label' => '<i class="fas fa-calendar-alt"></i> Appointments',
        'url' => ['/appointment/index'],
    ];
    $menuItems[] = [
        'label' => '<i class="fas fa-file-invoice"></i> Billing',
        'items' => [
            ['label' => '<i class="fas fa-file-invoice-dollar"></i> Bills', 'url' => ['/bill/index']],
            ['label' => '<i class="fas fa-list-alt"></i> Bill Items', 'url' => ['/bill-item/index']],
        ],
    ];
    $menuItems[] = [
        'label' => '<i class="fas fa-clipboard-list"></i> Clinical (View)',
        'items' => [
            ['label' => '<i class="fas fa-notes-medical"></i> Medical Records', 'url' => ['/medical-record/index']],
            ['label' => '<i class="fas fa-prescription"></i> Prescriptions', 'url' => ['/prescription/index']],
            ['label' => '<i class="fas fa-flask"></i> Lab Tests', 'url' => ['/lab-test/index']],
        ],
    ];
}

// ============================================
// DOCTOR MENU
// ============================================
elseif ($role === 'doctor') {
    // In the DIRECTOR MENU section, add this item:

    $menuItems[] = [
        'label' => '<i class="fas fa-procedures"></i> Patients',
        'url' => ['/patient/index'],
    ];
    $menuItems[] = [
        'label' => '<i class="fas fa-calendar-alt"></i> Appointments',
        'url' => ['/appointment/index'],
    ];
    $menuItems[] = [
        'label' => '<i class="fas fa-clipboard-list"></i> Clinical',
        'items' => [
            ['label' => '<i class="fas fa-notes-medical"></i> Medical Records', 'url' => ['/medical-record/index']],
            ['label' => '<i class="fas fa-prescription"></i> Prescriptions', 'url' => ['/prescription/index']],
            ['label' => '<i class="fas fa-flask"></i> Lab Tests', 'url' => ['/lab-test/index']],
            ['label' => '<i class="fas fa-tablets"></i> Medline', 'url' => ['/medline/index']],
        ],
    ];
    $menuItems[] = [
        'label' => '<i class="fas fa-file-invoice-dollar"></i> Bills (View)',
        'url' => ['/bill/index'],
    ];
}

// ============================================
// PATIENT MENU (Frontend)
// ============================================
elseif ($role === 'patient') {
    $menuItems[] = [
        'label' => '<i class="fas fa-calendar-plus"></i> Book Appointment',
        'url' => ['/appointment/create'],
    ];
    $menuItems[] = [
        'label' => '<i class="fas fa-calendar-check"></i> My Appointments',
        'url' => ['/appointment/index'],
    ];
    $menuItems[] = [
        'label' => '<i class="fas fa-notes-medical"></i> My Medical Records',
        'url' => ['/medical-record/index'],
    ];
    $menuItems[] = [
        'label' => '<i class="fas fa-prescription"></i> My Prescriptions',
        'url' => ['/prescription/index'],
    ];
    $menuItems[] = [
        'label' => '<i class="fas fa-file-invoice-dollar"></i> My Bills',
        'url' => ['/bill/index'],
    ];
    if ($user && $user->patient_id) {
        $menuItems[] = [
            'label' => '<i class="fas fa-user-edit"></i> My Profile',
            'url' => ['/profile/index'],
        ];
    }
}

// ============================================
// GUEST MENU
// ============================================
if ($isGuest) {
    $menuItems[] = [
        'label' => '<i class="fas fa-info-circle"></i> About',
        'url' => ['/site/about'],
    ];
    $menuItems[] = [
        'label' => '<i class="fas fa-envelope"></i> Contact',
        'url' => ['/site/contact'],
    ];
}

// ============================================
// USER MENU (Right side)
// ============================================
$userMenuItems = [];

if ($isGuest) {
    $userMenuItems[] = [
        'label' => '<i class="fas fa-user-plus"></i> Register',
        'url' => ['/site/signup'],
    ];
    $userMenuItems[] = [
        'label' => '<i class="fas fa-sign-in-alt"></i> Login',
        'url' => ['/site/login'],
    ];
} else {
    $userMenuItems[] = [
        'label' => '<i class="fas fa-user-circle"></i> ' . Html::encode($fullName) . ' <span class="badge bg-info">' . $roleLabel . '</span>',
        'items' => [
            ['label' => '<i class="fas fa-cog"></i> Profile Settings', 'url' => ['/profile/index']],
            ['label' => '<i class="fas fa-sign-out-alt"></i> Logout (' . Html::encode($user->username) . ')', 'url' => ['/site/logout'], 'linkOptions' => ['data-method' => 'post', 'class' => 'logout']],
        ],
    ];
}

?>
<header id="header">
    <?php NavBar::begin([
        'brandLabel' => Html::img('@web/images/medisync-logo-light.svg', [
                'alt' => 'MediSync',
                'height' => 40,
                'class' => 'd-inline-block align-top me-2'
            ]) . ' MediSync',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-expand-lg navbar-dark bg-dark fixed-top shadow',
            'id' => 'main-navbar',
        ],
    ]); ?>
    
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" 
            aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarContent">
        <?= Nav::widget([
            'options' => ['class' => 'navbar-nav me-auto'],
            'encodeLabels' => false,
            'items' => $menuItems,
        ]) ?>
        
        <?= Nav::widget([
            'options' => ['class' => 'navbar-nav ms-auto'],
            'encodeLabels' => false,
            'items' => $userMenuItems,
        ]) ?>
        
        <button id="theme-toggle" class="btn btn-link nav-link ms-2 fs-5" 
                aria-label="Toggle dark mode" title="Toggle dark/light mode">
            <i class="fas fa-moon" id="theme-icon"></i>
        </button>
    </div>
    
    <?php NavBar::end() ?>
</header>

<div style="padding-top: 70px;"></div>