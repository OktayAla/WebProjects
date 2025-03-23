<?php
require_once '../../includes/config.php';
?>
<div class="sidebar">
    <div class="sidebar-header">
        <i class="fas fa-paper-plane logo"></i>
        <h1><?= SITE_TITLE ?></h1>
    </div>
    
    <div class="sidebar-menu">
        <div class="menu-category">Ana Menü</div>
        <a href="dashboard.php" class="menu-item <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
        
        <div class="menu-category">İçerik Yönetimi</div>
        <a href="tarihi-yerler.php" class="menu-item <?= basename($_SERVER['PHP_SELF']) == 'tarihi-yerler.php' ? 'active' : '' ?>">
            <i class="fas fa-landmark"></i>
            <span>Tarihi Yerler</span>
        </a>
        <a href="dogal-guzellikler.php" class="menu-item <?= basename($_SERVER['PHP_SELF']) == 'dogal-guzellikler.php' ? 'active' : '' ?>">
            <i class="fas fa-mountain"></i>
            <span>Doğal Güzellikler</span>
        </a>
        <a href="lezzet-duraklari.php" class="menu-item <?= basename($_SERVER['PHP_SELF']) == 'lezzet-duraklari.php' ? 'active' : '' ?>">
            <i class="fas fa-utensils"></i>
            <span>Lezzet Durakları</span>
        </a>
    </div>
</div>