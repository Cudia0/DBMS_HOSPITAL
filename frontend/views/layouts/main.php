<?php

declare(strict_types=1);

/** @var yii\web\View $this */
/** @var string $content */

use common\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\helpers\Html;

$this->render('_head');
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100" data-bs-theme="light">
<head>
    <script>
    (function() {
        var savedTheme = localStorage.getItem('medisync-theme');
        if (savedTheme === 'dark') {
            document.documentElement.setAttribute('data-bs-theme', 'dark');
        }
    })();
    </script>
    <?php $this->head() ?>
    <title><?= Html::encode($this->title) ?></title>
</head>
<body class="d-flex flex-column h-100">
<?php $this->beginBody() ?>

<?= $this->render('_header') ?>

<main id="main" class="flex-grow-1" role="main">
    <div class="container-fluid px-4 py-3">
        <?php if (!empty($this->params['breadcrumbs'])): ?>
            <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs'], 'options' => ['class' => 'breadcrumb mb-3']]) ?>
        <?php endif ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</main>

<?= $this->render('_footer') ?>

<script>
(function() {
    function initThemeToggle() {
        var themeToggle = document.getElementById('theme-toggle');
        var themeIcon = document.getElementById('theme-icon');
        var htmlElement = document.documentElement;
        var navbar = document.getElementById('main-navbar');
        
        if (!themeToggle || !themeIcon) { setTimeout(initThemeToggle, 100); return; }
        
        function getCurrentTheme() { return htmlElement.getAttribute('data-bs-theme') || 'light'; }
        
        function updateThemeUI(theme) {
            if (theme === 'dark') {
                themeIcon.className = 'fas fa-sun';
                themeToggle.setAttribute('aria-label', 'Switch to light mode');
            } else {
                themeIcon.className = 'fas fa-moon';
                themeToggle.setAttribute('aria-label', 'Switch to dark mode');
            }
            if (navbar) {
                if (theme === 'dark') { navbar.classList.add('navbar-dark', 'bg-dark'); navbar.classList.remove('navbar-light', 'bg-light'); }
                else { navbar.classList.remove('navbar-dark', 'bg-dark'); navbar.classList.add('navbar-light', 'bg-light'); }
            }
        }
        
        function setTheme(theme) { htmlElement.setAttribute('data-bs-theme', theme); localStorage.setItem('medisync-theme', theme); updateThemeUI(theme); }
        function toggleTheme() { setTheme(getCurrentTheme() === 'dark' ? 'light' : 'dark'); }
        
        var savedTheme = localStorage.getItem('medisync-theme');
        if (savedTheme === 'dark' || savedTheme === 'light') { htmlElement.setAttribute('data-bs-theme', savedTheme); }
        updateThemeUI(getCurrentTheme());
        themeToggle.addEventListener('click', toggleTheme);
    }
    
    if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', initThemeToggle); }
    else { initThemeToggle(); }
})();
</script>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>