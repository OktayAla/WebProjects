<?php
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user role for conditional rendering
$userRole = $_SESSION['role'];
$userId = $_SESSION['user_id'];
$userName = $_SESSION['name'];
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRMS - İnsan Kaynakları Yönetim Sistemi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="app-container">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <img src="img/logo.png" alt="HRMS Logo" class="logo">
                <h3>HRMS</h3>
            </div>
            
            <div class="user-info">
                <div class="user-avatar">
                    <img src="img/avatars/default.png" alt="User Avatar">
                </div>
                <div class="user-details">
                    <h4><?php echo $userName; ?></h4>
                    <p><?php 
                    if($userRole == 'admin') echo 'İK Yöneticisi';
                    else if($userRole == 'manager') echo 'Birim Müdürü';
                    else echo 'Personel';
                    ?></p>
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="index.php" class="active"><i class="fas fa-home"></i> Ana Sayfa</a></li>
                    <li><a href="attendance.php"><i class="fas fa-clock"></i> Giriş/Çıkış Kayıtları</a></li>
                    <li><a href="leave_requests.php"><i class="fas fa-calendar-alt"></i> İzin Talepleri</a></li>
                    <li><a href="advance_requests.php"><i class="fas fa-money-bill-wave"></i> Avans Talepleri</a></li>
                    
                    <?php if($userRole == 'manager'): ?>
                    <li><a href="team_management.php"><i class="fas fa-users"></i> Ekip Yönetimi</a></li>
                    <li><a href="approval_requests.php"><i class="fas fa-tasks"></i> Onay Bekleyen Talepler</a></li>
                    <?php endif; ?>
                    
                    <?php if($userRole == 'admin'): ?>
                    <li><a href="employee_management.php"><i class="fas fa-user-cog"></i> Personel Yönetimi</a></li>
                    <li><a href="department_management.php"><i class="fas fa-building"></i> Departman Yönetimi</a></li>
                    <li><a href="card_management.php"><i class="fas fa-id-card"></i> Kart Yönetimi</a></li>
                    <li><a href="reports.php"><i class="fas fa-chart-bar"></i> Raporlar</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
            
            <div class="sidebar-footer">
                <a href="profile.php"><i class="fas fa-user-circle"></i> Profil</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Çıkış</a>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <header class="header">
                <div class="header-left">
                    <button id="sidebar-toggle" class="sidebar-toggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h2>Ana Sayfa</h2>
                </div>
                <div class="header-right">
                    <div class="notification">
                        <i class="fas fa-bell"></i>
                        <span class="badge">3</span>
                    </div>
                    <div class="date-time">
                        <span id="current-date"></span>
                        <span id="current-time"></span>
                    </div>
                </div>
            </header>
            
            <div class="content-wrapper">
                <div class="dashboard animate__animated animate__fadeIn">
                    <div class="welcome-card">
                        <div class="welcome-text">
                            <h3>Hoş Geldiniz, <?php echo $userName; ?>!</h3>
                            <p>İnsan Kaynakları Yönetim Sistemine hoş geldiniz. Bu panel üzerinden tüm insan kaynakları işlemlerinizi yönetebilirsiniz.</p>
                        </div>
                        <div class="welcome-image">
                            <img src="img/welcome.svg" alt="Welcome">
                        </div>
                    </div>
                    
                    <div class="row dashboard-stats">
                        <div class="col-md-3">
                            <div class="stat-card animate__animated animate__fadeInUp">
                                <div class="stat-card-body">
                                    <div class="stat-card-icon bg-primary">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div class="stat-card-info">
                                        <h5>Son Giriş</h5>
                                        <p id="last-entry">Yükleniyor...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="stat-card animate__animated animate__fadeInUp" style="animation-delay: 0.1s">
                                <div class="stat-card-body">
                                    <div class="stat-card-icon bg-success">
                                        <i class="fas fa-calendar-check"></i>
                                    </div>
                                    <div class="stat-card-info">
                                        <h5>Kalan İzin</h5>
                                        <p id="remaining-leave">Yükleniyor...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="stat-card animate__animated animate__fadeInUp" style="animation-delay: 0.2s">
                                <div class="stat-card-body">
                                    <div class="stat-card-icon bg-warning">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </div>
                                    <div class="stat-card-info">
                                        <h5>Avans Durumu</h5>
                                        <p id="advance-status">Yükleniyor...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="stat-card animate__animated animate__fadeInUp" style="animation-delay: 0.3s">
                                <div class="stat-card-body">
                                    <div class="stat-card-icon bg-info">
                                        <i class="fas fa-tasks"></i>
                                    </div>
                                    <div class="stat-card-info">
                                        <h5>Bekleyen Talepler</h5>
                                        <p id="pending-requests">Yükleniyor...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row dashboard-widgets">
                        <div class="col-md-6">
                            <div class="widget animate__animated animate__fadeInUp" style="animation-delay: 0.4s">
                                <div class="widget-header">
                                    <h4><i class="fas fa-calendar-alt"></i> Yaklaşan İzinler</h4>
                                </div>
                                <div class="widget-body">
                                    <div id="upcoming-leaves" class="widget-content">
                                        <div class="loading-spinner">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Yükleniyor...</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="widget animate__animated animate__fadeInUp" style="animation-delay: 0.5s">
                                <div class="widget-header">
                                    <h4><i class="fas fa-bell"></i> Bildirimler</h4>
                                </div>
                                <div class="widget-body">
                                    <div id="notifications" class="widget-content">
                                        <div class="loading-spinner">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Yükleniyor...</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if($userRole == 'manager' || $userRole == 'admin'): ?>
                    <div class="row dashboard-charts">
                        <div class="col-md-6">
                            <div class="widget animate__animated animate__fadeInUp" style="animation-delay: 0.6s">
                                <div class="widget-header">
                                    <h4><i class="fas fa-chart-line"></i> Departman Devam Durumu</h4>
                                </div>
                                <div class="widget-body">
                                    <canvas id="attendance-chart"></canvas>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="widget animate__animated animate__fadeInUp" style="animation-delay: 0.7s">
                                <div class="widget-header">
                                    <h4><i class="fas fa-chart-pie"></i> İzin Dağılımı</h4>
                                </div>
                                <div class="widget-body">
                                    <canvas id="leave-chart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="js/main.js"></script>
    <script src="js/dashboard.js"></script>
    <script>
        // Load user-specific data
        document.addEventListener('DOMContentLoaded', function() {
            // Load user data from JSON
            fetch(`js/users/${<?php echo $userId; ?>}.js`)
                .then(response => response.json())
                .then(userData => {
                    // Update dashboard with user data
                    updateDashboardData(userData);
                })
                .catch(error => {
                    console.error('Error loading user data:', error);
                });
        });
    </script>
</body>
</html>