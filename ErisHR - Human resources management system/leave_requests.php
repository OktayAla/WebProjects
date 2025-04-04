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

// Process form submission
$successMessage = '';
$errorMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    // Load leave requests data
    $leaveRequestsFile = 'data/leave_requests.json';
    $leaveRequests = [];
    
    if (file_exists($leaveRequestsFile)) {
        $leaveRequests = json_decode(file_get_contents($leaveRequestsFile), true);
    }
    
    // Process based on action
    if ($action === 'create') {
        // Get form data
        $startDate = $_POST['start_date'];
        $endDate = $_POST['end_date'];
        $leaveType = $_POST['leave_type'];
        $reason = $_POST['reason'];
        
        // Calculate number of days
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        $interval = $start->diff($end);
        $days = $interval->days + 1; // Include both start and end days
        
        // Load employees data to get employee details
        $employeesFile = 'data/employees.json';
        $employees = [];
        $employeeName = $userName;
        $employeeDepartment = $userDepartment;
        
        if (file_exists($employeesFile)) {
            $employees = json_decode(file_get_contents($employeesFile), true);
            
            foreach ($employees as $employee) {
                if ($employee['id'] == $userId) {
                    $employeeName = $employee['name'];
                    $employeeDepartment = $employee['department'];
                    break;
                }
            }
        }
        
        // Create new leave request
        $newRequest = [
            'id' => 'lr' . uniqid(),
            'employee_id' => $userId,
            'employee_name' => $employeeName,
            'department' => $employeeDepartment,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'days' => $days,
            'type' => $leaveType,
            'reason' => $reason,
            'status' => 'pending',
            'approved_by' => null,
            'approved_date' => null,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        // Add request to leave requests data
        $leaveRequests[] = $newRequest;
        
        // Save updated leave requests data
        file_put_contents($leaveRequestsFile, json_encode($leaveRequests, JSON_PRETTY_PRINT));
        
        $successMessage = "İzin talebiniz başarıyla oluşturuldu.";
    } elseif ($action === 'approve' || $action === 'reject') {
        // Check if user has manager or admin role
        if ($userRole !== 'manager' && $userRole !== 'admin') {
            $errorMessage = "Bu işlemi gerçekleştirmek için yetkiniz yok.";
        } else {
            $requestId = $_POST['request_id'];
            
            // Find request by ID
            $requestFound = false;
            
            foreach ($leaveRequests as &$request) {
                if ($request['id'] === $requestId) {
                    // Check if manager has permission to approve/reject
                    if ($userRole === 'manager' && $request['department'] !== $userDepartment) {
                        $errorMessage = "Sadece kendi departmanınızdaki talepleri onaylayabilirsiniz.";
                        break;
                    }
                    
                    $request['status'] = ($action === 'approve') ? 'approved' : 'rejected';
                    $request['approved_by'] = $userId;
                    $request['approved_date'] = date('Y-m-d H:i:s');
                    
                    // If approved, update employee's remaining leave
                    if ($action === 'approve') {
                        $employeesFile = 'data/employees.json';
                        
                        if (file_exists($employeesFile)) {
                            $employees = json_decode(file_get_contents($employeesFile), true);
                            
                            foreach ($employees as &$employee) {
                                if ($employee['id'] == $request['employee_id']) {
                                    $employee['remaining_leave'] -= $request['days'];
                                    if ($employee['remaining_leave'] < 0) {
                                        $employee['remaining_leave'] = 0;
                                    }
                                    break;
                                }
                            }
                            
                            // Save updated employees data
                            file_put_contents($employeesFile, json_encode($employees, JSON_PRETTY_PRINT));
                        }
                    }
                    
                    $requestFound = true;
                    break;
                }
            }
            
            if ($requestFound) {
                // Save updated leave requests data
                file_put_contents($leaveRequestsFile, json_encode($leaveRequests, JSON_PRETTY_PRINT));
                
                $successMessage = "İzin talebi başarıyla " . (($action === 'approve') ? 'onaylandı' : 'reddedildi') . ".";
            } else {
                $errorMessage = "İzin talebi bulunamadı.";
            }
        }
    } elseif ($action === 'delete') {
        $requestId = $_POST['request_id'];
        
        // Find request by ID
        $requestIndex = -1;
        
        foreach ($leaveRequests as $index => $request) {
            if ($request['id'] === $requestId) {
                // Check if user has permission to delete
                if ($userRole === 'admin' || ($request['employee_id'] == $userId && $request['status'] === 'pending')) {
                    $requestIndex = $index;
                } else {
                    $errorMessage = "Bu talebi silmek için yetkiniz yok.";
                }
                break;
            }
        }
        
        if ($requestIndex >= 0) {
            // Remove request from array
            array_splice($leaveRequests, $requestIndex, 1);
            
            // Save updated leave requests data
            file_put_contents($leaveRequestsFile, json_encode($leaveRequests, JSON_PRETTY_PRINT));
            
            $successMessage = "İzin talebi başarıyla silindi.";
        } else if (empty($errorMessage)) {
            $errorMessage = "İzin talebi bulunamadı.";
        }
    }
}

// Load leave requests for display
$leaveRequestsFile = 'data/leave_requests.json';
$leaveRequests = [];

if (file_exists($leaveRequestsFile)) {
    $allRequests = json_decode(file_get_contents($leaveRequestsFile), true);
    
    // Filter requests based on user role
    if ($userRole === 'admin') {
        // Admin sees all requests
        $leaveRequests = $allRequests;
    } elseif ($userRole === 'manager') {
        // Manager sees requests from their department
        foreach ($allRequests as $request) {
            if ($request['department'] === $userDepartment) {
                $leaveRequests[] = $request;
            }
        }
    } else {
        // Regular employee sees only their requests
        foreach ($allRequests as $request) {
            if ($request['employee_id'] == $userId) {
                $leaveRequests[] = $request;
            }
        }
    }
    
    // Sort requests by created_at (newest first)
    usort($leaveRequests, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
}

// Load employee data for remaining leave info
$employeesFile = 'data/employees.json';
$remainingLeave = 0;

if (file_exists($employeesFile)) {
    $employees = json_decode(file_get_contents($employeesFile), true);
    
    foreach ($employees as $employee) {
        if ($employee['id'] == $userId) {
            $remainingLeave = $employee['remaining_leave'];
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRMS - İzin Talepleri</title>
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
                    <li><a href="leave_requests.php" class="active"><i class="fas fa-calendar-alt"></i> İzin Talepleri</a></li>
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
                    <h2>İzin Talepleri</h2>
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
                <div class="leave-requests-page animate__animated animate__fadeIn">
                    <?php if (isset($successMessage)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <?php echo $successMessage; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (isset($errorMessage)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?php echo $errorMessage; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif; ?>
                    
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-info-circle me-2"></i>İzin Bilgilerim</h5>
                                </div>
                                <div class="card-body">
                                    <div class="leave-info">
                                        <div class="leave-info-item">
                                            <span class="leave-info-label">Kalan İzin Günü:</span>
                                            <span class="leave-info-value"><?php echo $remainingLeave; ?> gün</span>
                                        </div>
                                        <div class="progress mt-2 mb-4">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo ($remainingLeave / 20) * 100; ?>%" aria-valuenow="<?php echo $remainingLeave; ?>" aria-valuemin="0" aria-valuemax="20"></div>
                                        </div>
                                        <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#newLeaveRequestModal">
                                            <i class="fas fa-plus-circle me-2"></i>Yeni İzin Talebi
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5><i class="fas fa-calendar-alt me-2"></i>İzin Takvimi</h5>
                                    <div class="calendar-nav">
                                        <button class="btn btn-sm btn-outline-primary" id="prevMonth">
                                            <i class="fas fa-chevron-left"></i>
                                        </button>
                                        <span class="mx-2" id="currentMonth">Haziran 2023</span>
                                        <button class="btn btn-sm btn-outline-primary" id="nextMonth">
                                            <i class="fas fa-chevron-right"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="calendar-container" id="leaveCalendar">
                                        <!-- Calendar will be generated by JavaScript -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5><i class="fas fa-list me-2"></i>İzin Taleplerim</h5>
                            <div class="header-actions">
                                <button class="btn btn-sm btn-outline-primary" id="filterButton">
                                    <i class="fas fa-filter me-2"></i>Filtrele
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Filter Panel (Hidden by default) -->
                            <div class="filter-panel mb-4" style="display: none;">
                                <div class="card">
                                    <div class="card-body">
                                        <form id="filterForm" class="row g-3">
                                            <div class="col-md-4">
                                                <label for="filterStatus" class="form-label">Durum</label>
                                                <select class="form-select" id="filterStatus">
                                                    <option value="">Tümü</option>
                                                    <option value="pending">Beklemede</option>
                                                    <option value="approved">Onaylandı</option>
                                                    <option value="rejected">Reddedildi</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="filterType" class="form-label">İzin Tipi</label>
                                                <select class="form-select" id="filterType">
                                                    <option value="">Tümü</option>
                                                    <option value="annual">Yıllık İzin</option>
                                                    <option value="sick">Hastalık İzni</option>
                                                    <option value="marriage">Evlilik İzni</option>
                                                    <option value="maternity">Doğum İzni</option>
                                                    <option value="paternity">Babalık İzni</option>
                                                    <option value="bereavement">Ölüm İzni</option>
                                                    <option value="unpaid">Ücretsiz İzin</option>
                                                </select>
                                            </div>
                                            <?php if ($userRole === 'admin' || $userRole === 'manager'): ?>
                                            <div class="col-md-4">
                                                <label for="filterEmployee" class="form-label">Personel</label>
                                                <select class="form-select" id="filterEmployee">
                                                    <option value="">Tümü</option>
                                                    <!-- Employee options will be loaded dynamically -->
                                                </select>
                                            </div>
                                            <?php endif; ?>
                                            <div class="col-12 text-end">
                                                <button type="button" class="btn btn-secondary me-2" id="resetFilter">
                                                    <i class="fas fa-undo me-2"></i>Sıfırla
                                                </button>
                                                <button type="button" class="btn btn-primary" id="applyFilter">
                                                    <i class="fas fa-filter me-2"></i>Uygula
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Leave Requests List -->
                            <div class="leave-requests-list">
                                <?php if (empty($leaveRequests)): ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                                    <h5 class="text-muted">Henüz izin talebi bulunmuyor.</h5>
                                    <p class="text-muted">Yeni bir izin talebi oluşturmak için "Yeni İzin Talebi" butonuna tıklayın.</p>
                                </div>
                                <?php else: ?>
                                <?php foreach ($leaveRequests as $request): ?>
                                <div class="leave-request-card" 
                                     data-status="<?php echo $request['status']; ?>" 
                                     data-type="<?php echo $request['type']; ?>" 
                                     data-employee="<?php echo $request['employee_id']; ?>">
                                    <div class="leave-request-header">
                                        <h5 class="leave-request-title">
                                            <?php 
                                            $leaveTypeLabels = [
                                                'annual' => 'Yıllık İzin',
                                                'sick' => 'Hastalık İzni',
                                                'marriage' => 'Evlilik İzni',
                                                'maternity' => 'Doğum İzni',
                                                'paternity' => 'Babalık İzni',
                                                'bereavement' => 'Ölüm İzni',
                                                'unpaid' => 'Ücretsiz İzin'
                                            ];
                                            echo $leaveTypeLabels[$request['type']] ?? $request['type'];
                                            ?>
                                        </h5>
                                        <span class="leave-request-status status-<?php echo $request['status']; ?>">
                                            <?php 
                                            $statusLabels = [
                                                'pending' => 'Beklemede',
                                                'approved' => 'Onaylandı',
                                                'rejected' => 'Reddedildi'
                                            ];
                                            echo $statusLabels[$request['status']] ?? $request['status'];
                                            ?>
                                        </span>
                                    </div>
                                    <div class="leave-request-dates">
                                        <div class="leave-date">
                                            <div class="leave-date-label">Başlangıç Tarihi</div>
                                            <div class="leave-date-value"><?php echo date('d.m.Y', strtotime($request['start_date'])); ?></div>
                                        </div>
                                        <div class="leave-date">
                                            <div class="leave-date-label">Bitiş Tarihi</div>
                                            <div class="leave-date-value"><?php echo date('d.m.Y', strtotime($request['end_date'])); ?></div>
                                        </div>
                                        <div class="leave-date">
                                            <div class="leave-date-label">Toplam Gün</div>
                                            <div class="leave-date-value"><?php echo $request['days']; ?> gün</div>
                                        </div>
                                    </div>
                                    <?php if ($userRole === 'admin' || $userRole === 'manager'): ?>
                                    <div class="leave-request-employee mb-3">
                                        <h5>Personel</h5>
                                        <p><?php echo $request['employee_name']; ?> (<?php echo $request['department']; ?>)</p>
                                    </div>
                                    <?php endif; ?>
                                    <div class="leave-request-reason">
                                        <h5>İzin Nedeni</h5>
                                        <p><?php echo $request['reason']; ?></p>
                                    </div>
                                    <div class="leave-request-info">
                                        <small class="text-muted">Talep Tarihi: <?php echo date('d.m.Y H:i', strtotime($request['created_at'])); ?></small>
                                        <?php if ($request['status'] !== 'pending'): ?>
                                        <small class="text-muted ms-3">
                                            <?php echo ($request['status'] === 'approved') ? 'Onaylanma' : 'Reddedilme'; ?> Tarihi: 
                                            <?php echo date('d.m.Y H:i', strtotime($request['approved_date'])); ?>
                                        </small>
                                        <?php endif; ?>
                                    </div>
                                    <div class="leave-request-actions">
                                        <?php if ($request['status'] === 'pending'): ?>
                                            <?php if ($userRole === 'admin' || ($userRole === 'manager' && $request['department'] === $userDepartment)): ?>
                                            <form method="post" action="" class="d-inline">
                                                <input type="hidden" name="action" value="approve">
                                                <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                                <button type="submit" class="btn btn-success btn-sm">
                                                    <i class="fas fa-check me-1"></i> Onayla
                                                </button>
                                            </form>
                                            <form method="post" action="" class="d-inline">
                                                <input type="hidden" name="action" value="reject">
                                                <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-times me-1"></i> Reddet
                                                </button>
                                            </form>
                                            <?php endif; ?>
                                            <?php if ($request['employee_id'] == $userId): ?>
                                            <form method="post" action="" class="d-inline">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                                <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Bu izin talebini silmek istediğinize emin misiniz?')">
                                                    <i class="fas fa-trash me-1"></i> Sil
                                                </button>
                                            </form>
                                            <?php endif; ?>
                                        <?php elseif ($userRole === 'admin'): ?>
                                            <form method="post" action="" class="d-inline">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                                <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Bu izin talebini silmek istediğinize emin misiniz?')">
                                                    <i class="fas fa-trash me-1"></i> Sil
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- New Leave Request Modal -->
    <div class="modal fade" id="newLeaveRequestModal" tabindex="-1" aria-labelledby="newLeaveRequestModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newLeaveRequestModalLabel">Yeni İzin Talebi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="post" action="" id="leaveRequestForm">
                        <input type="hidden" name="action" value="create">
                        <div class="mb-3">
                            <label for="leave_type" class="form-label">İzin Tipi</label>
                            <select class="form-select" id="leave_type" name="leave_type" required>