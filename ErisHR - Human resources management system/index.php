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
                        <h3>Hoş Geldiniz, <?php echo $userName; ?>!</h3>
                        <p>İnsan Kaynakları Yönetim Sistemine hoş geldiniz. Bu panel üzerinden izin taleplerinizi, avans taleplerinizi ve giriş/çıkış kayıtlarınızı yönetebilirsiniz.</p>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-8">
                            <!-- Quick Stats -->
                            <div class="row mb-4">
                                <div class="col-md-4">
                                    <div class="stat-card bg-primary text-white">
                                        <div class="stat-card-body">
                                            <i class="fas fa-calendar-alt stat-icon"></i>
                                            <div class="stat-content">
                                                <h4 class="stat-value"><?php echo isset($_SESSION['remaining_leave']) ? $_SESSION['remaining_leave'] : 0; ?></h4>
                                                <p class="stat-label">Kalan İzin Günü</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="stat-card bg-success text-white">
                                        <div class="stat-card-body">
                                            <i class="fas fa-clock stat-icon"></i>
                                            <div class="stat-content">
                                                <h4 class="stat-value" id="workHours">8:00</h4>
                                                <p class="stat-label">Çalışma Saati</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="stat-card bg-info text-white">
                                        <div class="stat-card-body">
                                            <i class="fas fa-tasks stat-icon"></i>
                                            <div class="stat-content">
                                                <?php
                                                // Count pending requests
                                                $pendingRequests = 0;
                                                
                                                // Check for pending leave requests
                                                $leaveRequestsFile = 'data/leave_requests.json';
                                                if (file_exists($leaveRequestsFile)) {
                                                    $leaveRequests = json_decode(file_get_contents($leaveRequestsFile), true);
                                                    foreach ($leaveRequests as $request) {
                                                        if ($request['employee_id'] == $userId && $request['status'] === 'pending') {
                                                            $pendingRequests++;
                                                        }
                                                    }
                                                }
                                                
                                                // Check for pending advance requests
                                                $advanceRequestsFile = 'data/advance_requests.json';
                                                if (file_exists($advanceRequestsFile)) {
                                                    $advanceRequests = json_decode(file_get_contents($advanceRequestsFile), true);
                                                    foreach ($advanceRequests as $request) {
                                                        if ($request['employee_id'] == $userId && $request['status'] === 'pending') {
                                                            $pendingRequests++;
                                                        }
                                                    }
                                                }
                                                ?>
                                                <h4 class="stat-value"><?php echo $pendingRequests; ?></h4>
                                                <p class="stat-label">Bekleyen Talepler</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Recent Activity -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Son Aktiviteler</h5>
                                </div>
                                <div class="card-body">
                                    <ul class="activity-list">
                                        <?php
                                        // Get recent activities
                                        $activities = [];
                                        
                                        // Add recent leave requests
                                        if (file_exists($leaveRequestsFile)) {
                                            $leaveRequests = json_decode(file_get_contents($leaveRequestsFile), true);
                                            foreach ($leaveRequests as $request) {
                                                if ($request['employee_id'] == $userId) {
                                                    $activities[] = [
                                                        'type' => 'leave',
                                                        'status' => $request['status'],
                                                        'date' => $request['created_at'],
                                                        'details' => $request['start_date'] . ' - ' . $request['end_date'] . ' (' . $request['days'] . ' gün)'
                                                    ];
                                                }
                                            }
                                        }
                                        
                                        // Add recent advance requests
                                        if (file_exists($advanceRequestsFile)) {
                                            $advanceRequests = json_decode(file_get_contents($advanceRequestsFile), true);
                                            foreach ($advanceRequests as $request) {
                                                if ($request['employee_id'] == $userId) {
                                                    $activities[] = [
                                                        'type' => 'advance',
                                                        'status' => $request['status'],
                                                        'date' => $request['created_at'],
                                                        'details' => number_format($request['amount'], 2, ',', '.') . ' ₺'
                                                    ];
                                                }
                                            }
                                        }
                                        
                                        // Add recent attendance records
                                        $attendanceFile = 'data/attendance.json';
                                        if (file_exists($attendanceFile)) {
                                            $attendance = json_decode(file_get_contents($attendanceFile), true);
                                            foreach ($attendance as $record) {
                                                if ($record['employee_id'] == $userId) {
                                                    $activities[] = [
                                                        'type' => 'attendance',
                                                        'status' => $record['type'],
                                                        'date' => $record['timestamp'],
                                                        'details' => $record['date'] . ' ' . $record['time']
                                                    ];
                                                }
                                            }
                                        }
                                        
                                        // Sort activities by date (newest first)
                                        usort($activities, function($a, $b) {
                                            return strtotime($b['date']) - strtotime($a['date']);
                                        });
                                        
                                        // Display recent activities (limit to 5)
                                        $count = 0;
                                        foreach ($activities as $activity) {
                                            if ($count >= 5) break;
                                            
                                            echo '<li class="activity-item">';
                                            
                                            // Icon based on activity type
                                            if ($activity['type'] === 'leave') {
                                                echo '<i class="fas fa-calendar-alt activity-icon bg-primary"></i>';
                                            } elseif ($activity['type'] === 'advance') {
                                                echo '<i class="fas fa-money-bill-wave activity-icon bg-success"></i>';
                                            } elseif ($activity['type'] === 'attendance') {
                                                if ($activity['status'] === 'entry') {
                                                    echo '<i class="fas fa-sign-in-alt activity-icon bg-info"></i>';
                                                } else {
                                                    echo '<i class="fas fa-sign-out-alt activity-icon bg-warning"></i>';
                                                }
                                            }
                                            
                                            echo '<div class="activity-content">';
                                            
                                            // Activity description
                                            if ($activity['type'] === 'leave') {
                                                echo '<h6>İzin Talebi</h6>';
                                                echo '<p>' . $activity['details'] . '</p>';
                                            } elseif ($activity['type'] === 'advance') {
                                                echo '<h6>Avans Talebi</h6>';
                                                echo '<p>' . $activity['details'] . '</p>';
                                            } elseif ($activity['type'] === 'attendance') {
                                                if ($activity['status'] === 'entry') {
                                                    echo '<h6>Giriş Kaydı</h6>';
                                                } else {
                                                    echo '<h6>Çıkış Kaydı</h6>';
                                                }
                                                echo '<p>' . $activity['details'] . '</p>';
                                            }
                                            
                                            // Status badge
                                            if ($activity['type'] === 'leave' || $activity['type'] === 'advance') {
                                                echo '<span class="badge ';
                                                if ($activity['status'] === 'approved') {
                                                    echo 'bg-success">Onaylandı';
                                                } elseif ($activity['status'] === 'rejected') {
                                                    echo 'bg-danger">Reddedildi';
                                                } else {
                                                    echo 'bg-warning">Bekliyor';
                                                }
                                                echo '</span>';
                                            }
                                            
                                            // Date
                                            echo '<small class="text-muted">' . date('d.m.Y H:i', strtotime($activity['date'])) . '</small>';
                                            
                                            echo '</div></li>';
                                            
                                            $count++;
                                        }
                                        
                                        // If no activities found
                                        if (empty($activities)) {
                                            echo '<li class="activity-item"><div class="activity-content"><p>Henüz aktivite bulunmamaktadır.</p></div></li>';
                                        }
                                        ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <!-- Upcoming Leaves -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Yaklaşan İzinler</h5>
                                </div>
                                <div class="card-body">
                                    <?php
                                    // Get upcoming leaves
                                    $upcomingLeaves = [];
                                    $today = date('Y-m-d');
                                    
                                    if (file_exists($leaveRequestsFile)) {
                                        $leaveRequests = json_decode(file_get_contents($leaveRequestsFile), true);
                                        foreach ($leaveRequests as $request) {
                                            if ($request['status'] === 'approved' && $request['start_date'] >= $today) {
                                                // For all employees if admin/manager, otherwise only for current user
                                                if ($userRole === 'admin' || ($userRole === 'manager' && $request['department'] === $userDepartment) || $request['employee_id'] == $userId) {
                                                    $upcomingLeaves[] = $request;
                                                }
                                            }
                                        }
                                        
                                        // Sort by start date (soonest first)
                                        usort($upcomingLeaves, function($a, $b) {
                                            return strtotime($a['start_date']) - strtotime($b['start_date']);
                                        });
                                    }
                                    
                                    if (!empty($upcomingLeaves)) {
                                        echo '<ul class="leave-list">';
                                        $count = 0;
                                        foreach ($upcomingLeaves as $leave) {
                                            if ($count >= 5) break;
                                            
                                            echo '<li class="leave-item">';
                                            echo '<div class="leave-date">';
                                            echo '<span class="day">' . date('d', strtotime($leave['start_date'])) . '</span>';
                                            echo '<span class="month">' . date('M', strtotime($leave['start_date'])) . '</span>';
                                            echo '</div>';
                                            echo '<div class="leave-content">';
                                            echo '<h6>' . $leave['employee_name'] . '</h6>';
                                            echo '<p>' . date('d.m.Y', strtotime($leave['start_date'])) . ' - ' . date('d.m.Y', strtotime($leave['end_date'])) . ' (' . $leave['days'] . ' gün)</p>';
                                            echo '</div>';
                                            echo '</li>';
                                            
                                            $count++;
                                        }
                                        echo '</ul>';
                                    } else {
                                        echo '<p>Yaklaşan izin bulunmamaktadır.</p>';
                                    }
                                    ?>
                                </div>
                            </div>
                            
                            <!-- Notifications -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Bildirimler</h5>
                                </div>
                                <div class="card-body">
                                    <?php
                                    // Generate demo notifications
                                    $notifications = [
                                        [
                                            'icon' => 'fas fa-bell',
                                            'message' => 'Yeni bir duyuru yayınlandı: "Şirket Pikniği 15 Haziran\'da yapılacaktır."',
                                            'date' => date('Y-m-d H:i:s', strtotime('-2 hours')),
                                            'type' => 'info'
                                        ],
                                        [
                                            'icon' => 'fas fa-calendar-check',
                                            'message' => 'İzin talebiniz onaylandı.',
                                            'date' => date('Y-m-d H:i:s', strtotime('-1 day')),
                                            'type' => 'success'
                                        ],
                                        [
                                            'icon' => 'fas fa-money-check-alt',
                                            'message' => 'Maaş bordronuz hazırlandı.',
                                            'date' => date('Y-m-d H:i:s', strtotime('-3 days')),
                                            'type' => 'primary'
                                        ]
                                    ];
                                    
                                    echo '<ul class="notification-list">';
                                    foreach ($notifications as $notification) {
                                        echo '<li class="notification-item">';
                                        echo '<i class="' . $notification['icon'] . ' notification-icon bg-' . $notification['type'] . '"></i>';
                                        echo '<div class="notification-content">';
                                        echo '<p>' . $notification['message'] . '</p>';
                                        echo '<small class="text-muted">' . date('d.m.Y H:i', strtotime($notification['date'])) . '</small>';
                                        echo '</div>';
                                        echo '</li>';
                                    }
                                    echo '</ul>';
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/main.js"></script>
    <script>
        // Update current date and time
        function updateDateTime() {
            const now = new Date();
            const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit' };
            
            document.getElementById('current-date').textContent = now.toLocaleDateString('tr-TR', dateOptions);
            document.getElementById('current-time').textContent = now.toLocaleTimeString('tr-TR', timeOptions);
            
            setTimeout(updateDateTime, 1000);
        }
        
        updateDateTime();
        
        // Calculate work hours
        function updateWorkHours() {
            const now = new Date();
            const startTime = new Date();
            startTime.setHours(9, 0, 0); // Work starts at 9:00 AM
            
            if (now < startTime) {
                document.getElementById('workHours').textContent = '0:00';
                return;
            }
            
            const endTime = new Date();
            endTime.setHours(18, 0, 0); // Work ends at 6:00 PM
            
            const currentTime = now > endTime ? endTime : now;
            const diffMs = currentTime - startTime;
            const diffHrs = Math.floor(diffMs / 3600000);
            const diffMins = Math.floor((diffMs % 3600000) / 60000);
            
            document.getElementById('workHours').textContent = diffHrs + ':' + (diffMins < 10 ? '0' : '') + diffMins;
            
            setTimeout(updateWorkHours, 60000); // Update every minute
        }
        
        updateWorkHours();
    </script>
</body>
</html>