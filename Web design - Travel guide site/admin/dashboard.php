<?php
session_start();
require_once '../includes/config.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}

$historicalPlacesCount = count(glob('../pages/tarihi-yerler/*.php'));
$naturalBeautiesCount = count(glob('../pages/dogal-guzellikler/*.php'));
$flavorStopsCount = count(glob('../pages/lezzet-duraklari/*.php'));

$user = $_SESSION['admin_user'];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Paneli - <?= SITE_TITLE ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f5f5f5;
            color: #333;
        }

        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background-color: #2c3e50;
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #3d5166;
        }

        .sidebar-header .logo {
            font-size: 24px;
            color: #ff6b6b;
            margin-bottom: 10px;
        }

        .sidebar-header h1 {
            font-size: 18px;
            font-weight: 600;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .menu-category {
            padding: 10px 20px;
            font-size: 12px;
            text-transform: uppercase;
            color: #8795a1;
            font-weight: 600;
            letter-spacing: 1px;
        }

        .menu-item {
            padding: 12px 20px;
            display: flex;
            align-items: center;
            color: #ecf0f1;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .menu-item:hover {
            background-color: #34495e;
        }

        .menu-item.active {
            background-color: #3498db;
        }

        .menu-item i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .main-content {
            flex: 1;
            margin-left: 250px;
            padding: 20px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .user-info {
            display: flex;
            align-items: center;
        }

        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .user-name {
            font-weight: 500;
        }

        .user-role {
            font-size: 12px;
            color: #7f8c8d;
        }

        .logout-btn {
            background-color: #ff6b6b;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.3s;
        }

        .logout-btn:hover {
            background-color: #ff5252;
        }

        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            padding: 20px;
            display: flex;
            align-items: center;
        }

        .card-icon {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 24px;
        }

        .card-icon.blue {
            background-color: #e3f2fd;
            color: #2196f3;
        }

        .card-icon.green {
            background-color: #e8f5e9;
            color: #4caf50;
        }

        .card-icon.orange {
            background-color: #fff3e0;
            color: #ff9800;
        }

        .card-content h3 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .card-content p {
            color: #7f8c8d;
            font-size: 14px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }

        .activity-list {
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .activity-item {
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            align-items: center;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 16px;
        }

        .activity-icon.edit {
            background-color: #e3f2fd;
            color: #2196f3;
        }

        .activity-icon.add {
            background-color: #e8f5e9;
            color: #4caf50;
        }

        .activity-icon.delete {
            background-color: #ffebee;
            color: #f44336;
        }

        .activity-content {
            flex: 1;
        }

        .activity-content p {
            margin-bottom: 5px;
        }

        .activity-time {
            font-size: 12px;
            color: #7f8c8d;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 30px;
            margin-bottom: 30px;
        }

        .action-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            padding: 20px;
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
            text-decoration: none;
            color: #333;
        }

        .action-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .action-icon {
            font-size: 32px;
            margin-bottom: 15px;
            color: #3498db;
        }

        .action-title {
            font-weight: 600;
            font-size: 16px;
            margin-bottom: 8px;
            color: #2c3e50;
        }

        .action-desc {
            font-size: 13px;
            color: #7f8c8d;
            line-height: 1.4;
        }

        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
                overflow: visible;
            }

            .sidebar-header h1, .menu-item span, .menu-category {
                display: none;
            }

            .sidebar-header .logo {
                margin-bottom: 0;
            }

            .menu-item i {
                margin-right: 0;
                font-size: 18px;
            }

            .main-content {
                margin-left: 70px;
            }

            .dashboard-cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="sidebar">
            <div class="sidebar-header">
                <i class="fas fa-paper-plane logo"></i>
                <h1>Türkiye Gezi Rehberi</h1>
            </div>

            <div class="sidebar-menu">
                <div class="menu-category">Ana Menü</div>
                <a href="\turkiyegezirehberi\index.php" class="menu-item">
                    <i class="fas fa-home"></i>
                    <span>Anasayfa</span>
                </a>
                <a href="dashboard.php" class="menu-item active">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>

                <div class="menu-category">İçerik Yönetimi</div>
                <a href="tarihi-yerler.php" class="menu-item">
                    <i class="fas fa-landmark"></i>
                    <span>Tarihi Yerler</span>
                </a>
                <a href="dogal-guzellikler.php" class="menu-item">
                    <i class="fas fa-mountain"></i>
                    <span>Doğal Güzellikler</span>
                </a>
                <a href="lezzet-duraklari.php" class="menu-item">
                    <i class="fas fa-utensils"></i>
                    <span>Lezzet Durakları</span>
                </a>
            </div>
        </div>

        <div class="main-content">
            <div class="topbar">
                <h2>Dashboard</h2>

                <div class="user-info">
                    <a href="logout.php" class="logout-btn">Çıkış Yap</a>
                </div>
            </div>

            <div class="dashboard-cards">
                <div class="card">
                    <div class="card-icon blue">
                        <i class="fas fa-landmark"></i>
                    </div>
                    <div class="card-content">
                        <h3><?= $historicalPlacesCount ?></h3>
                        <p>Tarihi Yerler</p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-icon green">
                        <i class="fas fa-mountain"></i>
                    </div>
                    <div class="card-content">
                        <h3><?= $naturalBeautiesCount ?></h3>
                        <p>Doğal Güzellikler</p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-icon orange">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <div class="card-content">
                        <h3><?= $flavorStopsCount ?></h3>
                        <p>Lezzet Durakları</p>
                    </div>
                </div>
            </div>

            <h3 class="section-title">Hızlı İşlemler</h3>
            <div class="quick-actions">
                <a href="tarihi-yerler.php?action=add" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <div class="action-title">Tarihi Yer Ekle</div>
                    <div class="action-desc">Yeni bir tarihi yer ekleyin</div>
                </a>

                <a href="dogal-guzellikler.php?action=add" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <div class="action-title">Doğal Güzellik Ekle</div>
                    <div class="action-desc">Yeni bir doğal güzellik ekleyin</div>
                </a>

                <a href="lezzet-duraklari.php?action=add" class="action-card">
                    <div class="action-icon">
                        <i class="fas fa-plus-circle"></i>
                    </div>
                    <div class="action-title">Lezzet Durağı Ekle</div>
                    <div class="action-desc">Yeni bir lezzet durağı ekleyin</div>
                </a>
            </div>
        </div>
    </div>
</body>
</html>

<?php
$historicalPlacesFile = DATA_PATH . 'tarihi-yerler/items.json';
$naturalBeautiesFile = DATA_PATH . 'dogal-guzellikler/items.json';
$flavorStopsFile = DATA_PATH . 'lezzet-duraklari/items.json';

$historicalPlacesCount = file_exists($historicalPlacesFile) ? count(json_decode(file_get_contents($historicalPlacesFile), true) ?? []) : 0;
$naturalBeautiesCount = file_exists($naturalBeautiesFile) ? count(json_decode(file_get_contents($naturalBeautiesFile), true) ?? []) : 0;
$flavorStopsCount = file_exists($flavorStopsFile) ? count(json_decode(file_get_contents($flavorStopsFile), true) ?? []) : 0;
?>

<style>
.quick-actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 1rem;
    padding: 1rem 0;
}

.action-card {
    background: #fff;
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: transform 0.2s;
}

.action-card:hover {
    transform: translateY(-3px);
}

.action-title {
    font-size: 1.1rem;
    color: #2c3e50;
    margin-bottom: 0.5rem;
}

.action-description {
    font-size: 0.9rem;
    color: #7f8c8d;
    line-height: 1.4;
}
</style>