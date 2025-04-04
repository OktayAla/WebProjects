<?php
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user data
$userId = $_SESSION['user_id'];
$userName = $_SESSION['name'];
$userRole = $_SESSION['role'];
$userDepartment = $_SESSION['department'];

// Process PDKS card scan simulation
$scanSuccess = false;
$scanMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'scan_card') {
    $cardId = $_POST['card_id'];
    
    // Load cards data
    $cardsFile = 'data/cards.json';
    $cards = [];
    
    if (file_exists($cardsFile)) {
        $cards = json_decode(file_get_contents($cardsFile), true);
    }
    
    // Check if card exists and is assigned
    $cardFound = false;
    $employeeId = null;
    
    foreach ($cards as $card) {
        if ($card['card_id'] === $cardId) {
            $cardFound = true;
            if (!empty($card['employee_id'])) {
                $employeeId = $card['employee_id'];
            }
            break;
        }
    }
    
    if (!$cardFound) {
        $scanMessage = "Kart bulunamadı. Lütfen geçerli bir kart ID girin.";
    } elseif (empty($employeeId)) {
        $scanMessage = "Bu kart henüz bir personele atanmamış.";
    } else {
        // Load employees data to get employee name
        $employeesFile = 'data/employees.json';
        $employees = [];
        $employeeName = "Bilinmeyen Personel";
        
        if (file_exists($employeesFile)) {
            $employees = json_decode(file_get_contents($employeesFile), true);
            
            foreach ($employees as $employee) {
                if ($employee['id'] == $employeeId) {
                    $employeeName = $employee['name'];
                    break;
                }
            }
        }
        
        // Get current date and time
        $now = new DateTime();
        $date = $now->format('Y-m-d');
        $time = $now->format('H:i:s');
        
        // Load attendance records
        $attendanceFile = 'data/attendance.json';
        $attendance = [];
        
        if (file_exists($attendanceFile)) {
            $attendance = json_decode(file_get_contents($attendanceFile), true);
        }
        
        // Check if employee already has an entry for today
        $hasEntry = false;
        $hasExit = false;
        
        foreach ($attendance as $record) {
            if ($record['employee_id'] == $employeeId && $record['date'] === $date) {
                if ($record['type'] === 'entry') {
                    $hasEntry = true;
                } elseif ($record['type'] === 'exit') {
                    $hasExit = true;
                }
            }
        }
        
        // Determine record type (entry or exit)
        $recordType = 'entry';
        if ($hasEntry && !$hasExit) {
            $recordType = 'exit';
        } elseif ($hasEntry && $hasExit) {
            // Both entry and exit exist, create another entry/exit based on time
            $hour = (int)$now->format('H');
            if ($hour < 13) { // Before 1 PM, assume it's an entry
                $recordType = 'entry';
            } else { // After 1 PM, assume it's an exit
                $recordType = 'exit';
            }
        }
        
        // Create new attendance record
        $newRecord = [
            'id' => uniqid(),
            'employee_id' => $employeeId,
            'employee_name' => $employeeName,
            'card_id' => $cardId,
            'date' => $date,
            'time' => $time,
            'type' => $recordType,
            'timestamp' => $now->format('Y-m-d H:i:s')
        ];
        
        // Add record to attendance data
        $attendance[] = $newRecord;
        
        // Save updated attendance data
        file_put_contents($attendanceFile, json_encode($attendance, JSON_PRETTY_PRINT));
        
        // Set success message
        $scanSuccess = true;
        $scanMessage = "$employeeName için $recordType kaydı oluşturuldu. Saat: $time";
        
        // Create or update the employee's attendance JS file
        $employeeAttendanceDir = 'js/users';
        if (!file_exists($employeeAttendanceDir)) {
            mkdir($employeeAttendanceDir, 0777, true);
        }
        
        $employeeAttendanceFile = "$employeeAttendanceDir/$employeeId.js";
        
        // Get employee's attendance records
        $employeeRecords = [];
        foreach ($attendance as $record) {
            if ($record['employee_id'] == $employeeId) {
                $employeeRecords[] = $record;
            }
        }
        
        // Save employee's attendance records to JS file
        $jsContent = json_encode($employeeRecords, JSON_PRETTY_PRINT);
        file_put_contents($employeeAttendanceFile, $jsContent);
    }
}

// Load attendance records for display
$attendanceFile = 'data/attendance.json';
$attendanceRecords = [];

if (file_exists($attendanceFile)) {
    $allRecords = json_decode(file_get_contents($attendanceFile), true);
    
    // Filter records based on user role
    if ($userRole === 'admin') {
        // Admin sees all records
        $attendanceRecords = $allRecords;
    } elseif ($userRole === 'manager') {
        // Manager sees records from their department
        foreach ($allRecords as $record) {
            // Load employee data to check department
            $employeesFile = 'data/employees.json';
            if (file_exists($employeesFile)) {
                $employees = json_decode(file_get_contents($employeesFile), true);
                
                foreach ($employees as $employee) {
                    if ($employee['id'] == $record['employee_id'] && $employee['department'] === $userDepartment) {
                        $attendanceRecords[] = $record;
                        break;
                    }
                }
            }
        }
    } else {
        // Regular employee sees only their records
        foreach ($allRecords as $record) {
            if ($record['employee_id'] == $userId) {
                $attendanceRecords[] = $record;
            }
        }
    }
    
    // Sort records by date and time (newest first)
    usort($attendanceRecords, function($a, $b) {
        return strtotime($b['timestamp']) - strtotime($a['timestamp']);
    });
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRMS - Giriş/Çıkış Kayıtları</title>
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
                    <li><a href="index.php"><i class="fas fa-home"></i> Ana Sayfa</a></li>
                    <li><a href="attendance.php" class="active"><i class="fas fa-clock"></i> Giriş/Çıkış Kayıtları</a></li>
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
                    <h2>Giriş/Çıkış Kayıtları</h2>
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
                <div class="container-fluid">
                    <?php if (!empty($successMessage)): ?>
                    <div class="alert alert-success">
                        <?php echo $successMessage; ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($errorMessage)): ?>
                    <div class="alert alert-danger">
                        <?php echo $errorMessage; ?>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($userRole === 'manager' || $userRole === 'admin'): ?>
                    <!-- Tab navigation for managers to switch between personal and team attendance -->
                    <ul class="nav nav-tabs mb-4" id="attendanceTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal" type="button" role="tab" aria-controls="personal" aria-selected="true">
                                <i class="fas fa-user"></i> Kişisel Giriş/Çıkış Kayıtları
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="team-tab" data-bs-toggle="tab" data-bs-target="#team" type="button" role="tab" aria-controls="team" aria-selected="false">
                                <i class="fas fa-users"></i> Ekip Giriş/Çıkış Kayıtları
                            </button>
                        </li>
                    </ul>
                    
                    <div class="tab-content" id="attendanceTabsContent">
                        <div class="tab-pane fade show active" id="personal" role="tabpanel" aria-labelledby="personal-tab">
                    <?php endif; ?>
                    
                    <!-- Card Scan Simulation -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">PDKS Kart Okutma Simülasyonu</h5>
                        </div>
                        <div class="card-body">
                            <form action="" method="post" class="row g-3">
                                <input type="hidden" name="action" value="scan_card">
                                <div class="col-md-6">
                                    <label for="card_id" class="form-label">Kart ID</label>
                                    <input type="text" class="form-control" id="card_id" name="card_id" required>
                                </div>
                                <div class="col-md-6 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-id-card"></i> Kart Okut
                                    </button>
                                </div>
                            </form>
                            
                            <?php if (!empty($scanMessage)): ?>
                            <div class="alert <?php echo $scanSuccess ? 'alert-success' : 'alert-danger'; ?> mt-3">
                                <?php echo $scanMessage; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Attendance Records -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Giriş/Çıkış Kayıtları</h5>
                            <div>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="filterToday">
                                    <i class="fas fa-calendar-day"></i> Bugün
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="filterWeek">
                                    <i class="fas fa-calendar-week"></i> Bu Hafta
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="filterMonth">
                                    <i class="fas fa-calendar-alt"></i> Bu Ay
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover" id="attendanceTable">
                                    <thead>
                                        <tr>
                                            <th>Tarih</th>
                                            <th>Giriş Saati</th>
                                            <th>Çıkış Saati</th>
                                            <th>Toplam Süre</th>
                                            <th>Durum</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($userAttendanceRecords as $date => $record): ?>
                                        <tr data-date="<?php echo $date; ?>">
                                            <td><?php echo date('d.m.Y', strtotime($date)); ?></td>
                                            <td>
                                                <?php 
                                                if (isset($record['entry'])) {
                                                    echo date('H:i', strtotime($record['entry']));
                                                    
                                                    // Check if late entry (after 9:00 AM)
                                                    $entryTime = strtotime($record['entry']);
                                                    $startTime = strtotime('09:00:00');
                                                    
                                                    if ($entryTime > $startTime) {
                                                        echo ' <span class="badge bg-warning">Geç Giriş</span>';
                                                    }
                                                } else {
                                                    echo '-';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php 
                                                if (isset($record['exit'])) {
                                                    echo date('H:i', strtotime($record['exit']));
                                                    
                                                    // Check if early exit (before 6:00 PM)
                                                    $exitTime = strtotime($record['exit']);
                                                    $endTime = strtotime('18:00:00');
                                                    
                                                    if ($exitTime < $endTime) {
                                                        echo ' <span class="badge bg-warning">Erken Çıkış</span>';
                                                    }
                                                } else {
                                                    echo '-';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php 
                                                if (isset($record['entry']) && isset($record['exit'])) {
                                                    $entry = new DateTime($record['entry']);
                                                    $exit = new DateTime($record['exit']);
                                                    $interval = $entry->diff($exit);
                                                    echo $interval->format('%H:%I');
                                                } else {
                                                    echo '-';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php 
                                                if (isset($record['entry']) && isset($record['exit'])) {
                                                    echo '<span class="badge bg-success">Tamamlandı</span>';
                                                } elseif (isset($record['entry'])) {
                                                    echo '<span class="badge bg-primary">Devam Ediyor</span>';
                                                } else {
                                                    echo '<span class="badge bg-secondary">Belirsiz</span>';
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($userRole === 'manager' || $userRole === 'admin'): ?>
                        </div>
                        <div class="tab-pane fade" id="team" role="tabpanel" aria-labelledby="team-tab">
                            <!-- Team Attendance Records -->
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Ekip Giriş/Çıkış Kayıtları</h5>
                                    <div>
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="teamFilterToday">
                                            <i class="fas fa-calendar-day"></i> Bugün
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="teamFilterWeek">
                                            <i class="fas fa-calendar-week"></i> Bu Hafta
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="teamFilterMonth">
                                            <i class="fas fa-calendar-alt"></i> Bu Ay
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover" id="teamAttendanceTable">
                                            <thead>
                                                <tr>
                                                    <th>Personel</th>
                                                    <th>Tarih</th>
                                                    <th>Giriş Saati</th>
                                                    <th>Çıkış Saati</th>
                                                    <th>Toplam Süre</th>
                                                    <th>Durum</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($teamAttendanceRecords as $record): ?>
                                                <tr data-date="<?php echo $record['date']; ?>">
                                                    <td><?php echo $record['employee_name']; ?></td>
                                                    <td><?php echo date('d.m.Y', strtotime($record['date'])); ?></td>
                                                    <td>
                                                        <?php 
                                                        if (isset($record['entry'])) {
                                                            echo date('H:i', strtotime($record['entry']));
                                                            
                                                            // Check if late entry (after 9:00 AM)
                                                            $entryTime = strtotime($record['entry']);
                                                            $startTime = strtotime('09:00:00');
                                                            
                                                            if ($entryTime > $startTime) {
                                                                echo ' <span class="badge bg-warning">Geç Giriş</span>';
                                                            }
                                                        } else {
                                                            echo '-';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php 
                                                        if (isset($record['exit'])) {
                                                            echo date('H:i', strtotime($record['exit']));
                                                            
                                                            // Check if early exit (before 6:00 PM)
                                                            $exitTime = strtotime($record['exit']);
                                                            $endTime = strtotime('18:00:00');
                                                            
                                                            if ($exitTime < $endTime) {
                                                                echo ' <span class="badge bg-warning">Erken Çıkış</span>';
                                                            }
                                                        } else {
                                                            echo '-';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php 
                                                        if (isset($record['entry']) && isset($record['exit'])) {
                                                            $entry = new DateTime($record['entry']);
                                                            $exit = new DateTime($record['exit']);
                                                            $interval = $entry->diff($exit);
                                                            echo $interval->format('%H:%I');
                                                        } else {
                                                            echo '-';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php 
                                                        if (isset($record['entry']) && isset($record['exit'])) {
                                                            echo '<span class="badge bg-success">Tamamlandı</span>';
                                                        } elseif (isset($record['entry'])) {
                                                            echo '<span class="badge bg-primary">Devam Ediyor</span>';
                                                        } else {
                                                            echo '<span class="badge bg-secondary">Belirsiz</span>';
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/main.js"></script>
    <script>
        // Date filtering for personal attendance
        document.getElementById('filterToday').addEventListener('click', function() {
            filterAttendanceByDate('today', 'attendanceTable');
        });
        
        document.getElementById('filterWeek').addEventListener('click', function() {
            filterAttendanceByDate('week', 'attendanceTable');
        });
        
        document.getElementById('filterMonth').addEventListener('click', function() {
            filterAttendanceByDate('month', 'attendanceTable');
        });
        
        <?php if ($userRole === 'manager' || $userRole === 'admin'): ?>
        // Date filtering for team attendance
        document.getElementById('teamFilterToday').addEventListener('click', function() {
            filterAttendanceByDate('today', 'teamAttendanceTable');
        });
        
        document.getElementById('teamFilterWeek').addEventListener('click', function() {
            filterAttendanceByDate('week', 'teamAttendanceTable');
        });
        
        document.getElementById('teamFilterMonth').addEventListener('click', function() {
            filterAttendanceByDate('month', 'teamAttendanceTable');
        });
        <?php endif; ?>
        
        function filterAttendanceByDate(period, tableId) {
            const table = document.getElementById(tableId);
            const rows = table.querySelectorAll('tbody tr');
            
            const today = new Date();
            const oneWeekAgo = new Date();
            oneWeekAgo.setDate(today.getDate() - 7);
            const oneMonthAgo = new Date();
            oneMonthAgo.setMonth(today.getMonth() - 1);
            
            rows.forEach(row => {
                const dateStr = row.getAttribute('data-date');
                const rowDate = new Date(dateStr);
                
                if (period === 'today') {
                    if (rowDate.toDateString() === today.toDateString()) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                } else if (period === 'week') {
                    if (rowDate >= oneWeekAgo) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                } else if (period === 'month') {
                    if (rowDate >= oneMonthAgo) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
        }
    </script>
</body>
</html>