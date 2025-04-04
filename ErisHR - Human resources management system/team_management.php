<?php
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if user has manager role
if ($_SESSION['role'] !== 'manager' && $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// Get user data
$userId = $_SESSION['user_id'];
$userName = $_SESSION['name'];
$userRole = $_SESSION['role'];
$userDepartment = $_SESSION['department'];

// Load employees data
$employeesFile = 'data/employees.json';
$employees = [];
$teamMembers = [];

if (file_exists($employeesFile)) {
    $employees = json_decode(file_get_contents($employeesFile), true);
    
    // Filter employees to get team members (employees who report to this manager)
    foreach ($employees as $employee) {
        if ($employee['manager_id'] == $userId) {
            $teamMembers[] = $employee;
        }
    }
    
    // Sort team members by name
    usort($teamMembers, function($a, $b) {
        return strcmp($a['name'], $b['name']);
    });
}

// Load leave requests data
$leaveRequestsFile = 'data/leave_requests.json';
$leaveRequests = [];

if (file_exists($leaveRequestsFile)) {
    $allLeaveRequests = json_decode(file_get_contents($leaveRequestsFile), true);
    
    // Filter leave requests for team members
    foreach ($allLeaveRequests as $request) {
        foreach ($teamMembers as $member) {
            if ($request['employee_id'] == $member['id']) {
                $leaveRequests[] = $request;
                break;
            }
        }
    }
    
    // Sort leave requests by created_at (newest first)
    usort($leaveRequests, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
}

// Load advance requests data
$advanceRequestsFile = 'data/advance_requests.json';
$advanceRequests = [];

if (file_exists($advanceRequestsFile)) {
    $allAdvanceRequests = json_decode(file_get_contents($advanceRequestsFile), true);
    
    // Filter advance requests for team members
    foreach ($allAdvanceRequests as $request) {
        foreach ($teamMembers as $member) {
            if ($request['employee_id'] == $member['id']) {
                $advanceRequests[] = $request;
                break;
            }
        }
    }
    
    // Sort advance requests by created_at (newest first)
    usort($advanceRequests, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
}

// Load attendance data
$attendanceFile = 'data/attendance.json';
$attendance = [];

if (file_exists($attendanceFile)) {
    $allAttendance = json_decode(file_get_contents($attendanceFile), true);
    
    // Filter attendance records for team members (last 7 days)
    $sevenDaysAgo = date('Y-m-d', strtotime('-7 days'));
    
    foreach ($allAttendance as $record) {
        if ($record['date'] >= $sevenDaysAgo) {
            foreach ($teamMembers as $member) {
                if ($record['employee_id'] == $member['id']) {
                    $attendance[] = $record;
                    break;
                }
            }
        }
    }
    
    // Sort attendance records by date (newest first) and then by time
    usort($attendance, function($a, $b) {
        $dateCompare = strcmp($b['date'], $a['date']);
        if ($dateCompare === 0) {
            return strcmp($b['time'], $a['time']);
        }
        return $dateCompare;
    });
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRMS - Ekip Yönetimi</title>
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
                    <li><a href="attendance.php"><i class="fas fa-clock"></i> Giriş/Çıkış Kayıtları</a></li>
                    <li><a href="leave_requests.php"><i class="fas fa-calendar-alt"></i> İzin Talepleri</a></li>
                    <li><a href="advance_requests.php"><i class="fas fa-money-bill-wave"></i> Avans Talepleri</a></li>
                    
                    <?php if($userRole == 'manager' || $userRole == 'admin'): ?>
                    <li><a href="team_management.php" class="active"><i class="fas fa-users"></i> Ekip Yönetimi</a></li>
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
                    <h2>Ekip Yönetimi</h2>
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
                    <!-- Team Members Card -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Ekip Üyeleri</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($teamMembers)): ?>
                            <div class="alert alert-info">Henüz ekibinizde çalışan bulunmamaktadır.</div>
                            <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Ad Soyad</th>
                                            <th>Pozisyon</th>
                                            <th>E-posta</th>
                                            <th>Telefon</th>
                                            <th>İşe Başlama</th>
                                            <th>Kalan İzin</th>
                                            <th>Durum</th>
                                            <th>İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($teamMembers as $member): ?>
                                        <tr>
                                            <td><?php echo $member['name']; ?></td>
                                            <td><?php echo $member['position']; ?></td>
                                            <td><?php echo $member['email']; ?></td>
                                            <td><?php echo $member['phone']; ?></td>
                                            <td><?php echo $member['hire_date']; ?></td>
                                            <td><?php echo $member['remaining_leave']; ?> gün</td>
                                            <td>
                                                <span class="badge <?php echo ($member['status'] === 'active') ? 'bg-success' : 'bg-danger'; ?>">
                                                    <?php echo ($member['status'] === 'active') ? 'Aktif' : 'Pasif'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#viewEmployeeModal" 
                                                    data-id="<?php echo $member['id']; ?>"
                                                    data-name="<?php echo $member['name']; ?>"
                                                    data-department="<?php echo $member['department']; ?>"
                                                    data-position="<?php echo $member['position']; ?>"
                                                    data-email="<?php echo $member['email']; ?>"
                                                    data-phone="<?php echo $member['phone']; ?>"
                                                    data-hire-date="<?php echo $member['hire_date']; ?>"
                                                    data-annual-leave="<?php echo $member['annual_leave']; ?>"
                                                    data-remaining-leave="<?php echo $member['remaining_leave']; ?>">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Recent Leave Requests Card -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Son İzin Talepleri</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($leaveRequests)): ?>
                            <div class="alert alert-info">Henüz izin talebi bulunmamaktadır.</div>
                            <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Personel</th>
                                            <th>Başlangıç</th>
                                            <th>Bitiş</th>
                                            <th>Gün</th>
                                            <th>Tür</th>
                                            <th>Durum</th>
                                            <th>Tarih</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (array_slice($leaveRequests, 0, 5) as $request): ?>
                                        <tr>
                                            <td><?php echo $request['employee_name']; ?></td>
                                            <td><?php echo $request['start_date']; ?></td>
                                            <td><?php echo $request['end_date']; ?></td>
                                            <td><?php echo $request['days']; ?> gün</td>
                                            <td>
                                                <?php 
                                                if ($request['type'] === 'annual') echo 'Yıllık İzin';
                                                elseif ($request['type'] === 'sick') echo 'Hastalık';
                                                elseif ($request['type'] === 'marriage') echo 'Evlilik';
                                                elseif ($request['type'] === 'maternity') echo 'Doğum';
                                                elseif ($request['type'] === 'paternity') echo 'Babalık';
                                                elseif ($request['type'] === 'bereavement') echo 'Ölüm';
                                                else echo $request['type'];
                                                ?>
                                            </td>
                                            <td>
                                                <span class="badge <?php 
                                                if ($request['status'] === 'approved') echo 'bg-success';
                                                elseif ($request['status'] === 'rejected') echo 'bg-danger';
                                                else echo 'bg-warning';
                                                ?>">
                                                    <?php 
                                                    if ($request['status'] === 'approved') echo 'Onaylandı';
                                                    elseif ($request['status'] === 'rejected') echo 'Reddedildi';
                                                    else echo 'Bekliyor';
                                                    ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('d.m.Y', strtotime($request['created_at'])); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <?php if (count($leaveRequests) > 5): ?>
                                <div class="text-center mt-3">
                                    <a href="leave_requests.php" class="btn btn-sm btn-outline-primary">Tümünü Görüntüle</a>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Recent Advance Requests Card -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Son Avans Talepleri</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($advanceRequests)): ?>
                            <div class="alert alert-info">Henüz avans talebi bulunmamaktadır.</div>
                            <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Personel</th>
                                            <th>Miktar</th>
                                            <th>Sebep</th>
                                            <th>Durum</th>
                                            <th>Ödeme Tarihi</th>
                                            <th>Talep Tarihi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (array_slice($advanceRequests, 0, 5) as $request): ?>
                                        <tr>
                                            <td><?php echo $request['employee_name']; ?></td>
                                            <td><?php echo number_format($request['amount'], 2, ',', '.'); ?> ₺</td>
                                            <td><?php echo $request['reason']; ?></td>
                                            <td>
                                                <span class="badge <?php 
                                                if ($request['status'] === 'approved') echo 'bg-success';
                                                elseif ($request['status'] === 'rejected') echo 'bg-danger';
                                                else echo 'bg-warning';
                                                ?>">
                                                    <?php 
                                                    if ($request['status'] === 'approved') echo 'Onaylandı';
                                                    elseif ($request['status'] === 'rejected') echo 'Reddedildi';
                                                    else echo 'Bekliyor';
                                                    ?>
                                                </span>
                                            </td>
                                            <td><?php echo $request['payment_date'] ? date('d.m.Y', strtotime($request['payment_date'])) : '-'; ?></td>
                                            <td><?php echo date('d.m.Y', strtotime($request['created_at'])); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <?php if (count($advanceRequests) > 5): ?>
                                <div class="text-center mt-3">
                                    <a href="advance_requests.php" class="btn btn-sm btn-outline-primary">Tümünü Görüntüle</a>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Recent Attendance Records Card -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">Son Giriş/Çıkış Kayıtları</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($attendance)): ?>
                            <div class="alert alert-info">Henüz giriş/çıkış kaydı bulunmamaktadır.</div>
                            <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Personel</th>
                                            <th>Tarih</th>
                                            <th>Saat</th>
                                            <th>Tür</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach (array_slice($attendance, 0, 10) as $record): ?>
                                        <tr>
                                            <td>
                                                <?php 
                                                $employeeName = 'Bilinmeyen';
                                                foreach ($teamMembers as $member) {
                                                    if ($member['id'] == $record['employee_id']) {
                                                        $employeeName = $member['name'];
                                                        break;
                                                    }
                                                }
                                                echo $employeeName;
                                                ?>
                                            </td>
                                            <td><?php echo date('d.m.Y', strtotime($record['date'])); ?></td>
                                            <td><?php echo date('H:i', strtotime($record['time'])); ?></td>
                                            <td>
                                                <span class="badge <?php echo ($record['type'] === 'entry') ? 'bg-success' : 'bg-danger'; ?>">
                                                    <?php echo ($record['type'] === 'entry') ? 'Giriş' : 'Çıkış'; ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                <?php if (count($attendance) > 10): ?>
                                <div class="text-center mt-3">
                                    <a href="attendance.php" class="btn btn-sm btn-outline-primary">Tümünü Görüntüle</a>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- View Employee Modal -->
    <div class="modal fade" id="viewEmployeeModal" tabindex="-1" aria-labelledby="viewEmployeeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewEmployeeModalLabel">Personel Detayları</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>ID:</strong> <span id="view-id"></span></p>
                            <p><strong>Ad Soyad:</strong> <span id="view-name"></span></p>
                            <p><strong>Departman:</strong> <span id="view-department"></span></p>
                            <p><strong>Pozisyon:</strong> <span id="view-position"></span></p>
                            <p><strong>E-posta:</strong> <span id="view-email"></span></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Telefon:</strong> <span id="view-phone"></span></p>
                            <p><strong>İşe Başlama Tarihi:</strong> <span id="view-hire-date"></span></p>
                            <p><strong>Yıllık İzin Hakkı:</strong> <span id="view-annual-leave"></span></p>
                            <p><strong>Kalan İzin:</strong> <span id="view-remaining-leave"></span></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/main.js"></script>
    <script>
        // View Employee Modal
        document.querySelectorAll('[data-bs-target="#viewEmployeeModal"]').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const department = this.getAttribute('data-department');
                const position = this.getAttribute('data-position');
                const email = this.getAttribute('data-email');
                const phone = this.getAttribute('data-phone');
                const hireDate = this.getAttribute('data-hire-date');
                const annualLeave = this.getAttribute('data-annual-leave');
                const remainingLeave = this.getAttribute('data-remaining-leave');
                
                document.getElementById('view-id').textContent = id;
                document.getElementById('view-name').textContent = name;
                document.getElementById('view-department').textContent = department;
                document.getElementById('view-position').textContent = position;
                document.getElementById('view-email').textContent = email;
                document.getElementById('view-phone').textContent = phone;
                document.getElementById('view-hire-date').textContent = hireDate;
                document.getElementById('view-annual-leave').textContent = annualLeave;
                document.getElementById('view-remaining-leave').textContent = remainingLeave;
            });
        });
    </script>
</body>
</html>