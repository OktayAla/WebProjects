<?php
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if user has admin role
if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Get user data
$userId = $_SESSION['user_id'];
$userName = $_SESSION['name'];
$userRole = $_SESSION['role'];

// Load employees data
$employeesFile = 'data/employees.json';
$employees = [];
$departments = [];
$departmentEmployeeCounts = [];

if (file_exists($employeesFile)) {
    $employees = json_decode(file_get_contents($employeesFile), true);
    
    // Get unique departments and count employees
    foreach ($employees as $employee) {
        if (!in_array($employee['department'], $departments)) {
            $departments[] = $employee['department'];
            $departmentEmployeeCounts[$employee['department']] = 0;
        }
        $departmentEmployeeCounts[$employee['department']]++;
    }
    
    // Sort departments alphabetically
    sort($departments);
}

// Load leave requests data
$leaveRequestsFile = 'data/leave_requests.json';
$leaveRequests = [];
$leaveStatsByMonth = [];
$leaveStatsByDepartment = [];

if (file_exists($leaveRequestsFile)) {
    $leaveRequests = json_decode(file_get_contents($leaveRequestsFile), true);
    
    // Initialize stats arrays
    for ($i = 1; $i <= 12; $i++) {
        $month = date('F', mktime(0, 0, 0, $i, 1));
        $leaveStatsByMonth[$month] = 0;
    }
    
    foreach ($departments as $dept) {
        $leaveStatsByDepartment[$dept] = 0;
    }
    
    // Calculate leave stats
    foreach ($leaveRequests as $request) {
        if ($request['status'] === 'approved') {
            // By month
            $month = date('F', strtotime($request['start_date']));
            $leaveStatsByMonth[$month] += $request['days'];
            
            // By department
            $leaveStatsByDepartment[$request['department']] += $request['days'];
        }
    }
}

// Load attendance data
$attendanceFile = 'data/attendance.json';
$attendance = [];
$attendanceStatsByMonth = [];
$attendanceStatsByDepartment = [];
$lateEntryStatsByMonth = [];

if (file_exists($attendanceFile)) {
    $attendance = json_decode(file_get_contents($attendanceFile), true);
    
    // Initialize stats arrays
    for ($i = 1; $i <= 12; $i++) {
        $month = date('F', mktime(0, 0, 0, $i, 1));
        $attendanceStatsByMonth[$month] = 0;
        $lateEntryStatsByMonth[$month] = 0;
    }
    
    foreach ($departments as $dept) {
        $attendanceStatsByDepartment[$dept] = [
            'total' => 0,
            'late' => 0
        ];
    }
    
    // Calculate attendance stats
    foreach ($attendance as $record) {
        if ($record['type'] === 'entry') {
            // Get employee department
            $employeeDept = '';
            foreach ($employees as $employee) {
                if ($employee['id'] == $record['employee_id']) {
                    $employeeDept = $employee['department'];
                    break;
                }
            }
            
            // By month
            $month = date('F', strtotime($record['date']));
            $attendanceStatsByMonth[$month]++;
            
            // Check if late entry (after 9:00 AM)
            $entryTime = strtotime($record['time']);
            $startTime = strtotime('09:00:00');
            
            if ($entryTime > $startTime) {
                $lateEntryStatsByMonth[$month]++;
                
                // By department
                if (!empty($employeeDept)) {
                    $attendanceStatsByDepartment[$employeeDept]['late']++;
                }
            }
            
            // By department
            if (!empty($employeeDept)) {
                $attendanceStatsByDepartment[$employeeDept]['total']++;
            }
        }
    }
}

// Load advance requests data
$advanceRequestsFile = 'data/advance_requests.json';
$advanceRequests = [];
$advanceStatsByMonth = [];
$advanceStatsByDepartment = [];

if (file_exists($advanceRequestsFile)) {
    $advanceRequests = json_decode(file_get_contents($advanceRequestsFile), true);
    
    // Initialize stats arrays
    for ($i = 1; $i <= 12; $i++) {
        $month = date('F', mktime(0, 0, 0, $i, 1));
        $advanceStatsByMonth[$month] = 0;
    }
    
    foreach ($departments as $dept) {
        $advanceStatsByDepartment[$dept] = 0;
    }
    
    // Calculate advance stats
    foreach ($advanceRequests as $request) {
        if ($request['status'] === 'approved') {
            // By month
            $month = date('F', strtotime($request['created_at']));
            $advanceStatsByMonth[$month] += $request['amount'];
            
            // By department
            $advanceStatsByDepartment[$request['department']] += $request['amount'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRMS - Raporlar</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css">
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                    <li><a href="reports.php" class="active"><i class="fas fa-chart-bar"></i> Raporlar</a></li>
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
                    <h2>Raporlar</h2>
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
                    <!-- Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Toplam Personel</h5>
                                    <h2 class="card-text"><?php echo count($employees); ?></h2>
                                    <i class="fas fa-users fa-2x card-icon"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Toplam Departman</h5>
                                    <h2 class="card-text"><?php echo count($departments); ?></h2>
                                    <i class="fas fa-building fa-2x card-icon"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Onaylanan İzinler</h5>
                                    <h2 class="card-text">
                                        <?php 
                                        $approvedLeaves = 0;
                                        foreach ($leaveRequests as $request) {
                                            if ($request['status'] === 'approved') {
                                                $approvedLeaves++;
                                            }
                                        }
                                        echo $approvedLeaves;
                                        ?>
                                    </h2>
                                    <i class="fas fa-calendar-check fa-2x card-icon"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Onaylanan Avanslar</h5>
                                    <h2 class="card-text">
                                        <?php 
                                        $approvedAdvances = 0;
                                        foreach ($advanceRequests as $request) {
                                            if ($request['status'] === 'approved') {
                                                $approvedAdvances++;
                                            }
                                        }
                                        echo $approvedAdvances;
                                        ?>
                                    </h2>
                                    <i class="fas fa-money-bill-wave fa-2x card-icon"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Department Distribution Chart -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Departman Dağılımı</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="departmentChart"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Aylık İzin Kullanımı</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="leaveChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Attendance Charts -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Aylık Giriş İstatistikleri</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="attendanceChart"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Geç Giriş İstatistikleri</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="lateEntryChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Advance Payment Chart -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Aylık Avans Ödemeleri</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="advanceChart"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Departman Bazlı Avans Dağılımı</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="departmentAdvanceChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Department Performance Table -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Departman Performans Özeti</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Departman</th>
                                                    <th>Personel Sayısı</th>
                                                    <th>Kullanılan İzin (Gün)</th>
                                                    <th>Toplam Giriş Sayısı</th>
                                                    <th>Geç Giriş Sayısı</th>
                                                    <th>Geç Giriş Oranı</th>
                                                    <th>Toplam Avans (₺)</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($departments as $dept): ?>
                                                <tr>
                                                    <td><?php echo $dept; ?></td>
                                                    <td><?php echo $departmentEmployeeCounts[$dept]; ?></td>
                                                    <td><?php echo $leaveStatsByDepartment[$dept]; ?></td>
                                                    <td><?php echo $attendanceStatsByDepartment[$dept]['total']; ?></td>
                                                    <td><?php echo $attendanceStatsByDepartment[$dept]['late']; ?></td>
                                                    <td>
                                                        <?php 
                                                        $lateRatio = 0;
                                                        if ($attendanceStatsByDepartment[$dept]['total'] > 0) {
                                                            $lateRatio = ($attendanceStatsByDepartment[$dept]['late'] / $attendanceStatsByDepartment[$dept]['total']) * 100;
                                                        }
                                                        echo number_format($lateRatio, 2) . '%';
                                                        ?>
                                                    </td>
                                                    <td><?php echo number_format($advanceStatsByDepartment[$dept], 2, ',', '.'); ?> ₺</td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Hidden elements to store chart data -->
    <div id="department-data-labels" style="display: none;"><?php echo json_encode(array_keys($departmentEmployeeCounts)); ?></div>
    <div id="department-data-values" style="display: none;"><?php echo json_encode(array_values($departmentEmployeeCounts)); ?></div>
    
    <div id="leave-data-labels" style="display: none;"><?php echo json_encode(array_keys($leaveStatsByMonth)); ?></div>
    <div id="leave-data-values" style="display: none;"><?php echo json_encode(array_values($leaveStatsByMonth)); ?></div>
    
    <div id="attendance-data-labels" style="display: none;"><?php echo json_encode(array_keys($attendanceStatsByMonth)); ?></div>
    <div id="attendance-data-values" style="display: none;"><?php echo json_encode(array_values($attendanceStatsByMonth)); ?></div>
    
    <div id="late-entry-data-labels" style="display: none;"><?php echo json_encode(array_keys($lateEntryStatsByMonth)); ?></div>
    <div id="late-entry-data-values" style="display: none;"><?php echo json_encode(array_values($lateEntryStatsByMonth)); ?></div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/main.js"></script>
    <script src="js/reports.js"></script>
        // Department Distribution Chart
        const departmentCtx = document.getElementById('departmentChart').getContext('2d');
        const departmentChart = new Chart(departmentCtx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode(array_keys($departmentEmployeeCounts)); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_values($departmentEmployeeCounts)); ?>,
                    backgroundColor: [
                        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#5a5c69', '#858796',
                        '#6610f2', '#6f42c1', '#fd7e14', '#20c9a6', '#27a844'
                    ],
                    hoverBackgroundColor: [
                        '#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#be2617', '#3a3b45', '#60616f',
                        '#5d0cdb', '#5d37a8', '#dc6a03', '#169b80', '#1e8e39'
                    ],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }]
            },
            options: {
                maintainAspectRatio: false,
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    caretPadding: 10,
                },
                legend: {
                    display: true,
                    position: 'bottom'
                },
                cutoutPercentage: 0,
            },
        });
        
        // Monthly Leave Usage Chart
        const leaveCtx = document.getElementById('leaveChart').getContext('2d');
        const leaveChart = new Chart(leaveCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_keys($leaveStatsByMonth)); ?>,
                datasets: [{
                    label: 'Kullanılan İzin Günleri',
                    data: <?php echo json_encode(array_values($leaveStatsByMonth)); ?>,
                    backgroundColor: '#4e73df',
                    borderColor: '#4e73df',
                    borderWidth: 1
                }]
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
        
        // Monthly Attendance Chart
        const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
        const attendanceChart = new Chart(attendanceCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_keys($attendanceStatsByMonth)); ?>,
                datasets: [{
                    label: 'Toplam Giriş Sayısı',
                    data: <?php echo json_encode(array_values($attendanceStatsByMonth)); ?>,
                    backgroundColor: 'rgba(28, 200, 138, 0.2)',
                    borderColor: '#1cc88a',
                    borderWidth: 2,
                    pointBackgroundColor: '#1cc88a',
                    pointBorderColor: '#1cc88a',
                    pointHoverBackgroundColor: '#1cc88a',
                    pointHoverBorderColor: '#1cc88a',
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    fill: true
                }]
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
        
        // Late Entry Chart
        const lateEntryCtx = document.getElementById('lateEntryChart').getContext('2d');
        const lateEntryChart = new Chart(lateEntryCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_keys($lateEntryStatsByMonth)); ?>,
                datasets: [{
                    label: 'Geç Giriş Sayısı',
                    data: <?php echo json_encode(array_values($lateEntryStatsByMonth)); ?>,
                    backgroundColor: '#e74a3b',
                    borderColor: '#e74a3b',
                    borderWidth: 1
                }]
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });
        
        // Monthly Advance Payment Chart
        const advanceCtx = document.getElementById('advanceChart').getContext('2d');
        const advanceChart = new Chart(advanceCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_keys($advanceStatsByMonth)); ?>,
                datasets: [{
                    label: 'Avans Ödemeleri (₺)',
                    data: <?php echo json_encode(array_values($advanceStatsByMonth)); ?>,
                    backgroundColor: '#36b9cc',
                    borderColor: '#36b9cc',
                    borderWidth: 1
                }]
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            callback: function(value) {
                                return value.toLocaleString('tr-TR', { style: 'currency', currency: 'TRY' });
                            }
                        }
                    }]
                },
                tooltips: {
                    callbacks: {
                        label: function(tooltipItem, data) {
                            return tooltipItem.yLabel.toLocaleString('tr-TR', { style: 'currency', currency: 'TRY' });
                        }
                    }
                }
            }
        });
        
        // Department Advance Distribution Chart
        const departmentAdvanceCtx = document.getElementById('departmentAdvanceChart').getContext('2d');
        const departmentAdvanceChart = new Chart(departmentAdvanceCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode(array_keys($advanceStatsByDepartment)); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_values($advanceStatsByDepartment)); ?>,
                    backgroundColor: [
                        '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#5a5c69', '#858796',
                        '#6610f2', '#6f42c1', '#fd7e14', '#20c9a6', '#27a844'
                    ],
                    hoverBackgroundColor: [
                        '#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#be2617', '#3a3b45', '#60616f',
                        '#5d0cdb', '#5d37a8', '#dc6a03', '#169b80', '#1e8e39'
                    ],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }]
            },
            options: {
                maintainAspectRatio: false,
                tooltips: {
                    backgroundColor: "rgb(255,255,255)",
                    bodyFontColor: "#858796",
                    borderColor: '#dddfeb',
                    borderWidth: 1,
                    xPadding: 15,
                    yPadding: 15,
                    displayColors: false,
                    caretPadding: 10,
                    callbacks: {
                        label: function(tooltipItem, data) {
                            const value = data.datasets[0].data[tooltipItem.index];
                            return value.toLocaleString('tr-TR', { style: 'currency', currency: 'TRY' });
                        }
                    }
                },
                legend: {
                    display: true,
                    position: 'bottom'
                },
                cutoutPercentage: 50,
            },
        });
    </script>
</body>
</html>