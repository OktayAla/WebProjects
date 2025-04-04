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
                <div class="attendance-page animate__animated animate__fadeIn">
                    <!-- PDKS Card Reader Simulation -->
                    <div class="row mb-4">
                        <div class="col-md-6 mx-auto">
                            <div class="pdks-reader animate__animated animate__fadeInUp">
                                <div class="pdks-icon">
                                    <i class="fas fa-id-card"></i>
                                </div>
                                <h3 class="pdks-status">PDKS Kart Okuyucu Simülasyonu</h3>
                                <p class="text-muted mb-4">Giriş veya çıkış kaydı oluşturmak için kart ID'sini girin.</p>
                                
                                <?php if ($scanSuccess): ?>
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <?php echo $scanMessage; ?>
                                </div>
                                <?php elseif (!empty($scanMessage)): ?>
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    <?php echo $scanMessage; ?>
                                </div>
                                <?php endif; ?>
                                
                                <form method="post" action="" class="mb-4">
                                    <input type="hidden" name="action" value="scan_card">
                                    <div class="input-group pdks-input">
                                        <input type="text" class="form-control" name="card_id" placeholder="Kart ID girin" required>
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fas fa-check me-2"></i>Kart Okut
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Attendance Records -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5><i class="fas fa-history me-2"></i>Giriş/Çıkış Kayıtları</h5>
                            <div class="header-actions">
                                <button class="btn btn-sm btn-outline-primary" id="filterButton">
                                    <i class="fas fa-filter me-2"></i>Filtrele
                                </button>
                                <?php if ($userRole === 'admin'): ?>
                                <button class="btn btn-sm btn-outline-success ms-2" id="exportButton">
                                    <i class="fas fa-file-excel me-2"></i>Dışa Aktar
                                </button>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Filter Panel (Hidden by default) -->
                            <div class="filter-panel mb-4" style="display: none;">
                                <div class="card">
                                    <div class="card-body">
                                        <form id="filterForm" class="row g-3">
                                            <div class="col-md-4">
                                                <label for="filterDate" class="form-label">Tarih</label>
                                                <input type="date" class="form-control" id="filterDate">
                                            </div>
                                            <div class="col-md-4">
                                                <label for="filterType" class="form-label">Kayıt Tipi</label>
                                                <select class="form-select" id="filterType">
                                                    <option value="">Tümü</option>
                                                    <option value="entry">Giriş</option>
                                                    <option value="exit">Çıkış</option>
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
                            
                            <!-- Attendance Table -->
                            <div class="table-responsive">
                                <table class="table table-hover" id="attendanceTable">
                                    <thead>
                                        <tr>
                                            <th>Tarih</th>
                                            <th>Saat</th>
                                            <th>Personel</th>
                                            <th>Kayıt Tipi</th>
                                            <th>Kart ID</th>
                                            <?php if ($userRole === 'admin'): ?>
                                            <th>İşlemler</th>
                                            <?php endif; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($attendanceRecords)): ?>
                                        <tr>
                                            <td colspan="<?php echo ($userRole === 'admin') ? '6' : '5'; ?>" class="text-center">Kayıt bulunamadı.</td>
                                        </tr>
                                        <?php else: ?>
                                        <?php foreach ($attendanceRecords as $record): ?>
                                        <tr class="record-row" 
                                            data-date="<?php echo $record['date']; ?>" 
                                            data-type="<?php echo $record['type']; ?>" 
                                            data-employee="<?php echo $record['employee_id']; ?>">
                                            <td><?php echo date('d.m.Y', strtotime($record['date'])); ?></td>
                                            <td><?php echo $record['time']; ?></td>
                                            <td><?php echo $record['employee_name']; ?></td>
                                            <td>
                                                <?php if ($record['type'] === 'entry'): ?>
                                                <span class="badge bg-success">Giriş</span>
                                                <?php else: ?>
                                                <span class="badge bg-danger">Çıkış</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo $record['card_id']; ?></td>
                                            <?php if ($userRole === 'admin'): ?>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary edit-record" data-id="<?php echo $record['id']; ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger delete-record" data-id="<?php echo $record['id']; ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                            <?php endif; ?>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Edit Record Modal (Admin Only) -->
    <?php if ($userRole === 'admin'): ?>
    <div class="modal fade" id="editRecordModal" tabindex="-1" aria-labelledby="editRecordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editRecordModalLabel">Kayıt Düzenle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editRecordForm">
                        <input type="hidden" id="editRecordId">
                        <div class="mb-3">
                            <label for="editDate" class="form-label">Tarih</label>
                            <input type="date" class="form-control" id="editDate" required>
                        </div>
                        <div class="mb-3">
                            <label for="editTime" class="form-label">Saat</label>
                            <input type="time" class="form-control" id="editTime" required>
                        </div>
                        <div class="mb-3">
                            <label for="editType" class="form-label">Kayıt Tipi</label>
                            <select class="form-select" id="editType" required>
                                <option value="entry">Giriş</option>
                                <option value="exit">Çıkış</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="button" class="btn btn-primary" id="saveRecordChanges">Kaydet</button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
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
            const filterDate = document.getElementById('filterDate');
            const filterType = document.getElementById('filterType');
            const filterEmployee = document.getElementById('filterEmployee');
            const recordRows = document.querySelectorAll('.record-row');
            
            if (applyFilter && resetFilter) {
                applyFilter.addEventListener('click', function() {
                    const dateValue = filterDate.value;
                    const typeValue = filterType.value;
                    const employeeValue = filterEmployee ? filterEmployee.value : '';
                    
                    recordRows.forEach(row => {
                        let showRow = true;
                        
                        if (dateValue && row.dataset.date !== dateValue) {
                            showRow = false;
                        }
                        
                        if (typeValue && row.dataset.type !== typeValue) {
                            showRow = false;
                        }
                        
                        if (employeeValue && row.dataset.employee !== employeeValue) {
                            showRow = false;
                        }
                        
                        row.style.display = showRow ? '' : 'none';
                    });
                    
                    filterPanel.style.display = 'none';
                });
                
                resetFilter.addEventListener('click', function() {
                    filterDate.value = '';
                    filterType.value = '';
                    if (filterEmployee) filterEmployee.value = '';
                    
                    recordRows.forEach(row => {
                        row.style.display = '';
                    });
                });
            }
            
            <?php if ($userRole === 'admin'): ?>
            // Edit record functionality
            const editButtons = document.querySelectorAll('.edit-record');
            const editRecordModal = new bootstrap.Modal(document.getElementById('editRecordModal'));
            const editRecordId = document.getElementById('editRecordId');
            const editDate = document.getElementById('editDate');
            const editTime = document.getElementById('editTime');
            const editType = document.getElementById('editType');
            const saveRecordChanges = document.getElementById('saveRecordChanges');
            
            editButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const recordId = this.dataset.id;
                    const row = this.closest('tr');
                    const date = row.querySelector('td:nth-child(1)').textContent.split('.');
                    const formattedDate = `${date[2]}-${date[1]}-${date[0]}`; // Convert DD.MM.YYYY to YYYY-MM-DD
                    const time = row.querySelector('td:nth-child(2)').textContent;
                    const type = row.querySelector('td:nth-child(4) .badge').textContent === 'Giriş' ? 'entry' : 'exit';
                    
                    editRecordId.value = recordId;
                    editDate.value = formattedDate;
                    editTime.value = time;
                    editType.value = type;
                    
                    editRecordModal.show();
                });
            });
            
            if (saveRecordChanges) {
                saveRecordChanges.addEventListener('click', function() {
                    // In a real application, this would send an AJAX request to update the record
                    // For this demo, we'll just show a success message and reload the page
                    alert('Kayıt başarıyla güncellendi.');
                    location.reload();
                });
            }
            
            // Delete record functionality
            const deleteButtons = document.querySelectorAll('.delete-record');
            
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    if (confirm('Bu kaydı silmek istediğinize emin misiniz?')) {
                        // In a real application, this would send an AJAX request to delete the record
                        // For this demo, we'll just show a success message and reload the page
                        alert('Kayıt başarıyla silindi.');
                        location.reload();
                    }
                });
            });
            
            // Export functionality
            const exportButton = document.getElementById('exportButton');
            
            if (exportButton) {
                exportButton.addEventListener('click', function() {
                    alert('Kayıtlar Excel dosyasına aktarıldı.');
                });
            }
            <?php endif; ?>
        });
    </script>
</body>
</html>