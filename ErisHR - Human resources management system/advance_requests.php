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
    
    // Load advance requests data
    $advanceRequestsFile = 'data/advance_requests.json';
    $advanceRequests = [];
    
    if (file_exists($advanceRequestsFile)) {
        $advanceRequests = json_decode(file_get_contents($advanceRequestsFile), true);
    }
    
    // Process based on action
    if ($action === 'create') {
        // Get form data
        $amount = $_POST['amount'];
        $reason = $_POST['reason'];
        
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
        
        // Create new advance request
        $newRequest = [
            'id' => 'ar' . uniqid(),
            'employee_id' => $userId,
            'employee_name' => $employeeName,
            'department' => $employeeDepartment,
            'amount' => (float)$amount,
            'reason' => $reason,
            'status' => 'pending',
            'approved_by' => null,
            'approved_date' => null,
            'payment_date' => null,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        // Add request to advance requests data
        $advanceRequests[] = $newRequest;
        
        // Save updated advance requests data
        file_put_contents($advanceRequestsFile, json_encode($advanceRequests, JSON_PRETTY_PRINT));
        
        $successMessage = "Avans talebiniz başarıyla oluşturuldu.";
    } elseif ($action === 'approve' || $action === 'reject') {
        // Check if user has manager or admin role
        if ($userRole !== 'manager' && $userRole !== 'admin') {
            $errorMessage = "Bu işlemi gerçekleştirmek için yetkiniz yok.";
        } else {
            $requestId = $_POST['request_id'];
            
            // Find request by ID
            $requestFound = false;
            
            foreach ($advanceRequests as &$request) {
                if ($request['id'] === $requestId) {
                    // Check if manager has permission to approve/reject
                    if ($userRole === 'manager' && $request['department'] !== $userDepartment) {
                        $errorMessage = "Sadece kendi departmanınızdaki talepleri onaylayabilirsiniz.";
                        break;
                    }
                    
                    $request['status'] = ($action === 'approve') ? 'approved' : 'rejected';
                    $request['approved_by'] = $userId;
                    $request['approved_date'] = date('Y-m-d H:i:s');
                    
                    // If approved, set payment date (3 days from now)
                    if ($action === 'approve') {
                        $paymentDate = new DateTime();
                        $paymentDate->add(new DateInterval('P3D')); // Add 3 days
                        $request['payment_date'] = $paymentDate->format('Y-m-d');
                    }
                    
                    $requestFound = true;
                    break;
                }
            }
            
            if ($requestFound) {
                // Save updated advance requests data
                file_put_contents($advanceRequestsFile, json_encode($advanceRequests, JSON_PRETTY_PRINT));
                
                $successMessage = "Avans talebi başarıyla " . (($action === 'approve') ? 'onaylandı' : 'reddedildi') . ".";
            } else {
                $errorMessage = "Avans talebi bulunamadı.";
            }
        }
    } elseif ($action === 'delete') {
        $requestId = $_POST['request_id'];
        
        // Find request by ID
        $requestIndex = -1;
        
        foreach ($advanceRequests as $index => $request) {
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
            array_splice($advanceRequests, $requestIndex, 1);
            
            // Save updated advance requests data
            file_put_contents($advanceRequestsFile, json_encode($advanceRequests, JSON_PRETTY_PRINT));
            
            $successMessage = "Avans talebi başarıyla silindi.";
        } else if (empty($errorMessage)) {
            $errorMessage = "Avans talebi bulunamadı.";
        }
    }
}

// Load advance requests for display
$advanceRequestsFile = 'data/advance_requests.json';
$advanceRequests = [];

if (file_exists($advanceRequestsFile)) {
    $allRequests = json_decode(file_get_contents($advanceRequestsFile), true);
    
    // Filter requests based on user role
    if ($userRole === 'admin') {
        // Admin sees all requests
        $advanceRequests = $allRequests;
    } elseif ($userRole === 'manager') {
        // Manager sees requests from their department
        foreach ($allRequests as $request) {
            if ($request['department'] === $userDepartment) {
                $advanceRequests[] = $request;
            }
        }
    } else {
        // Regular employee sees only their requests
        foreach ($allRequests as $request) {
            if ($request['employee_id'] == $userId) {
                $advanceRequests[] = $request;
            }
        }
    }
    
    // Sort requests by created_at (newest first)
    usort($advanceRequests, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRMS - Avans Talepleri</title>
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
                    <li><a href="advance_requests.php" class="active"><i class="fas fa-money-bill-wave"></i> Avans Talepleri</a></li>
                    
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
                    <h2>Avans Talepleri</h2>
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
                <div class="advance-requests-page animate__animated animate__fadeIn">
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
                                    <h5><i class="fas fa-info-circle me-2"></i>Avans Bilgileri</h5>
                                </div>
                                <div class="card-body">
                                    <div class="advance-info">
                                        <p class="mb-4">Avans talepleri, acil durumlarda veya özel ihtiyaçlar için çalışanlara finansal destek sağlamak amacıyla kullanılır. Talepleriniz ilgili yöneticiniz tarafından değerlendirilecektir.</p>
                                        <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#newAdvanceRequestModal">
                                            <i class="fas fa-plus-circle me-2"></i>Yeni Avans Talebi
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-chart-line me-2"></i>Avans İstatistikleri</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="stat-item">
                                                <div class="stat-icon bg-primary">
                                                    <i class="fas fa-money-bill-wave"></i>
                                                </div>
                                                <div class="stat-info">
                                                    <h5>Toplam Avans</h5>
                                                    <p id="total-advance">Hesaplanıyor...</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="stat-item">
                                                <div class="stat-icon bg-success">
                                                    <i class="fas fa-check-circle"></i>
                                                </div>
                                                <div class="stat-info">
                                                    <h5>Onaylanan</h5>
                                                    <p id="approved-advance">Hesaplanıyor...</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="stat-item">
                                                <div class="stat-icon bg-warning">
                                                    <i class="fas fa-clock"></i>
                                                </div>
                                                <div class="stat-info">
                                                    <h5>Bekleyen</h5>
                                                    <p id="pending-advance">Hesaplanıyor...</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5><i class="fas fa-list me-2"></i>Avans Taleplerim</h5>
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
                                                <label for="filterAmount" class="form-label">Tutar</label>
                                                <select class="form-select" id="filterAmount">
                                                    <option value="">Tümü</option>
                                                    <option value="0-1000">0 - 1.000 ₺</option>
                                                    <option value="1000-3000">1.000 - 3.000 ₺</option>
                                                    <option value="3000+">3.000 ₺ ve üzeri</option>
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
                            
                            <!-- Advance Requests List -->
                            <div class="advance-requests-list">
                                <?php if (empty($advanceRequests)): ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-money-bill-wave fa-4x text-muted mb-3"></i>
                                    <h5 class="text-muted">Henüz avans talebi bulunmuyor.</h5>
                                    <p class="text-muted">Yeni bir avans talebi oluşturmak için "Yeni Avans Talebi" butonuna tıklayın.</p>
                                </div>
                                <?php else: ?>
                                <?php foreach ($advanceRequests as $request): ?>
                                <div class="advance-request-card" 
                                     data-status="<?php echo $request['status']; ?>" 
                                     data-amount="<?php echo $request['amount']; ?>" 
                                     data-employee="<?php echo $request['employee_id']; ?>">
                                    <div class="advance-request-header">
                                        <h5 class="advance-request-title">
                                            <?php echo number_format($request['amount'], 2, ',', '.'); ?> ₺
                                        </h5>
                                        <span class="advance-request-status status-<?php echo $request['status']; ?>">
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
                                    <?php if ($userRole === 'admin' || $userRole === 'manager'): ?>
                                    <div class="advance-request-employee mb-3">
                                        <h5>Personel</h5>
                                        <p><?php echo $request['employee_name']; ?> (<?php echo $request['department']; ?>)</p>
                                    </div>
                                    <?php endif; ?>
                                    <div class="advance-request-reason">
                                        <h5>Avans Nedeni</h5>
                                        <p><?php echo $request['reason']; ?></p>
                                    </div>
                                    <?php if ($request['status'] === 'approved' && !empty($request['payment_date'])): ?>
                                    <div class="advance-request-payment">
                                        <h5>Ödeme Tarihi</h5>
                                        <p><?php echo date('d.m.Y', strtotime($request['payment_date'])); ?></p>
                                    </div>
                                    <?php endif; ?>
                                    <div class="advance-request-info">
                                        <small class="text-muted">Talep Tarihi: <?php echo date('d.m.Y H:i', strtotime($request['created_at'])); ?></small>
                                        <?php if ($request['status'] !== 'pending'): ?>
                                        <small class="text-muted ms-3">
                                            <?php echo ($request['status'] === 'approved') ? 'Onaylanma' : 'Reddedilme'; ?> Tarihi: 
                                            <?php echo date('d.m.Y H:i', strtotime($request['approved_date'])); ?>
                                        </small>
                                        <?php endif; ?>
                                    </div>
                                    <div class="advance-request-actions">
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
                                                <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Bu avans talebini silmek istediğinize emin misiniz?')">
                                                    <i class="fas fa-trash me-1"></i> Sil
                                                </button>
                                            </form>
                                            <?php endif; ?>
                                        <?php elseif ($userRole === 'admin'): ?>
                                            <form method="post" action="" class="d-inline">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                                <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Bu avans talebini silmek istediğinize emin misiniz?')">
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
    
    <!-- New Advance Request Modal -->
    <div class="modal fade" id="newAdvanceRequestModal" tabindex="-1" aria-labelledby="newAdvanceRequestModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newAdvanceRequestModalLabel">Yeni Avans Talebi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="post" action="" id="advanceRequestForm">
                        <input type="hidden" name="action" value="create">
                        <div class="mb-3">
                            <label for="amount" class="form-label">Avans Tutarı (₺)</label>
                            <input type="number" class="form-control" id="amount" name="amount" min="100" step="100" required>
                            <div class="form-text">Talep etmek istediğiniz avans tutarını giriniz.</div>
                        </div>
                        <div class="mb-3">
                            <label for="reason" class="form-label">Avans Nedeni</label>
                            <textarea class="form-control" id="reason" name="reason" rows="4" required></textarea>
                            <div class="form-text">Avans talebinizin nedenini detaylı bir şekilde açıklayınız.</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" form="advanceRequestForm" class="btn btn-primary">Talep Oluştur</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Filter panel toggle
            const filterButton = document.getElementById('filterButton');
            const filterPanel = document.querySelector('.filter-panel');
            
            if (filterButton && filterPanel) {
                filterButton.addEventListener('click', function() {
                    filterPanel.style.display = filterPanel.style.display === 'none' ? 'block' : 'none';
                });
            }
            
            // Apply filter
            const applyFilter = document.getElementById('applyFilter');
            const resetFilter = document.getElementById('resetFilter');
            const filterStatus = document.getElementById('filterStatus');
            const filterAmount = document.getElementById('filterAmount');
            const filterEmployee = document.getElementById('filterEmployee');
            const requestCards = document.querySelectorAll('.advance-request-card');
            
            if (applyFilter && resetFilter) {
                applyFilter.addEventListener('click', function() {
                    const statusValue = filterStatus.value;
                    const amountValue = filterAmount.value;
                    const employeeValue = filterEmployee ? filterEmployee.value : '';
                    
                    requestCards.forEach(card => {
                        let showCard = true;
                        
                        if (statusValue && card.dataset.status !== statusValue) {
                            showCard = false;
                        }
                        
                        if (amountValue) {
                            const amount = parseFloat(card.dataset.amount);
                            if (amountValue === '0-1000' && (amount > 1000 || amount <= 0)) {
                                showCard = false;
                            } else if (amountValue === '1000-3000' && (amount <= 1000 || amount > 3000)) {
                                showCard = false;
                            } else if (amountValue === '3000+' && amount <= 3000) {
                                showCard = false;
                            }
                        }
                        
                        if (employeeValue && card.dataset.employee !== employeeValue) {
                            showCard = false;
                        }
                        
                        card.style.display = showCard ? '' : 'none';
                    });
                    
                    filterPanel.style.display = 'none';
                });
                
                resetFilter.addEventListener('click', function() {
                    filterStatus.value = '';
                    filterAmount.value = '';
                    if (filterEmployee) filterEmployee.value = '';
                    
                    requestCards.forEach(card => {
                        card.style.display = '';
                    });
                });
            }
            
            // Calculate statistics
            calculateAdvanceStatistics();
        });
        
        /**
         * Calculate advance statistics
         */
        function calculateAdvanceStatistics() {
            const totalAdvanceElement = document.getElementById('total-advance');
            const approvedAdvanceElement = document.getElementById('approved-advance');
            const pendingAdvanceElement = document.getElementById('pending-advance');
            
            if (totalAdvanceElement && approvedAdvanceElement && pendingAdvanceElement) {
                const requestCards = document.querySelectorAll('.advance-request-card');
                let totalAmount = 0;
                let approvedAmount = 0;
                let pendingAmount = 0;
                
                requestCards.forEach(card => {
                    const amount = parseFloat(card.dataset.amount);
                    const status = card.dataset.status;
                    
                    totalAmount += amount;
                    
                    if (status === 'approved') {
                        approvedAmount += amount;
                    } else if (status === 'pending') {
                        pendingAmount += amount;
                    }
                });
                
                // Format amounts as currency
                const formatter = new Intl.NumberFormat('tr-TR', {
                    style: 'currency',
                    currency: 'TRY'
                });
                
                totalAdvanceElement.textContent = formatter.format(totalAmount);
                approvedAdvanceElement.textContent = formatter.format(approvedAmount);
                pendingAdvanceElement.textContent = formatter.format(pendingAmount);
            }
        }
    </script>
</body>
</html>