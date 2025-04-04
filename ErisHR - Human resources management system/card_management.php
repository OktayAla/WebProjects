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

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    // Load existing cards data
    $cardsFile = 'data/cards.json';
    $cards = [];
    
    if (file_exists($cardsFile)) {
        $cards = json_decode(file_get_contents($cardsFile), true);
    }
    
    // Process based on action
    if ($action === 'assign') {
        $cardId = $_POST['card_id'];
        $employeeId = $_POST['employee_id'];
        
        // Check if card already exists
        $cardExists = false;
        foreach ($cards as &$card) {
            if ($card['card_id'] === $cardId) {
                $card['employee_id'] = $employeeId;
                $card['assigned_date'] = date('Y-m-d H:i:s');
                $cardExists = true;
                break;
            }
        }
        
        // If card doesn't exist, create new entry
        if (!$cardExists) {
            $cards[] = [
                'card_id' => $cardId,
                'employee_id' => $employeeId,
                'assigned_date' => date('Y-m-d H:i:s'),
                'status' => 'active'
            ];
        }
        
        // Save updated cards data
        file_put_contents($cardsFile, json_encode($cards, JSON_PRETTY_PRINT));
        
        $successMessage = "Kart başarıyla atandı.";
    } elseif ($action === 'unassign') {
        $cardId = $_POST['card_id'];
        
        // Find and unassign card
        foreach ($cards as &$card) {
            if ($card['card_id'] === $cardId) {
                $card['employee_id'] = null;
                $card['assigned_date'] = null;
                $card['status'] = 'inactive';
                break;
            }
        }
        
        // Save updated cards data
        file_put_contents($cardsFile, json_encode($cards, JSON_PRETTY_PRINT));
        
        $successMessage = "Kart ataması başarıyla kaldırıldı.";
    } elseif ($action === 'add') {
        $cardId = $_POST['new_card_id'];
        
        // Check if card already exists
        $cardExists = false;
        foreach ($cards as $card) {
            if ($card['card_id'] === $cardId) {
                $cardExists = true;
                break;
            }
        }
        
        // If card doesn't exist, add it
        if (!$cardExists) {
            $cards[] = [
                'card_id' => $cardId,
                'employee_id' => null,
                'assigned_date' => null,
                'status' => 'inactive'
            ];
            
            // Save updated cards data
            file_put_contents($cardsFile, json_encode($cards, JSON_PRETTY_PRINT));
            
            $successMessage = "Yeni kart başarıyla eklendi.";
        } else {
            $errorMessage = "Bu kart ID'si zaten sistemde kayıtlı.";
        }
    } elseif ($action === 'delete') {
        $cardId = $_POST['card_id'];
        
        // Remove card from array
        foreach ($cards as $key => $card) {
            if ($card['card_id'] === $cardId) {
                unset($cards[$key]);
                break;
            }
        }
        
        // Reindex array
        $cards = array_values($cards);
        
        // Save updated cards data
        file_put_contents($cardsFile, json_encode($cards, JSON_PRETTY_PRINT));
        
        $successMessage = "Kart başarıyla silindi.";
    }
}

// Load employees data
$employeesFile = 'data/employees.json';
$employees = [];

if (file_exists($employeesFile)) {
    $employees = json_decode(file_get_contents($employeesFile), true);
}

// Load cards data
$cardsFile = 'data/cards.json';
$cards = [];

if (file_exists($cardsFile)) {
    $cards = json_decode(file_get_contents($cardsFile), true);
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRMS - Kart Yönetimi</title>
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
                    
                    <?php if($userRole == 'manager'): ?>
                    <li><a href="team_management.php"><i class="fas fa-users"></i> Ekip Yönetimi</a></li>
                    <li><a href="approval_requests.php"><i class="fas fa-tasks"></i> Onay Bekleyen Talepler</a></li>
                    <?php endif; ?>
                    
                    <?php if($userRole == 'admin'): ?>
                    <li><a href="employee_management.php"><i class="fas fa-user-cog"></i> Personel Yönetimi</a></li>
                    <li><a href="department_management.php"><i class="fas fa-building"></i> Departman Yönetimi</a></li>
                    <li><a href="card_management.php" class="active"><i class="fas fa-id-card"></i> Kart Yönetimi</a></li>
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
                    <h2>Kart Yönetimi</h2>
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
                <div class="card-management animate__animated animate__fadeIn">
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
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-plus-circle me-2"></i>Yeni Kart Ekle</h5>
                                </div>
                                <div class="card-body">
                                    <form method="post" action="">
                                        <input type="hidden" name="action" value="add">
                                        <div class="mb-3">
                                            <label for="new_card_id" class="form-label">Kart ID</label>
                                            <input type="text" class="form-control" id="new_card_id" name="new_card_id" required>
                                            <div class="form-text">RFID kart numarasını giriniz.</div>
                                        </div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-plus-circle me-2"></i>Kart Ekle
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5><i class="fas fa-link me-2"></i>Kart Ata</h5>
                                </div>
                                <div class="card-body">
                                    <form method="post" action="">
                                        <input type="hidden" name="action" value="assign">
                                        <div class="mb-3">
                                            <label for="card_id" class="form-label">Kart Seçin</label>
                                            <select class="form-select" id="card_id" name="card_id" required>
                                                <option value="">Kart seçin</option>
                                                <?php foreach ($cards as $card): ?>
                                                <option value="<?php echo $card['card_id']; ?>">
                                                    <?php echo $card['card_id']; ?> 
                                                    <?php if (!empty($card['employee_id'])): ?>
                                                    (Atanmış)
                                                    <?php endif; ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="employee_id" class="form-label">Personel Seçin</label>
                                            <select class="form-select" id="employee_id" name="employee_id" required>
                                                <option value="">Personel seçin</option>
                                                <?php foreach ($employees as $employee): ?>
                                                <option value="<?php echo $employee['id']; ?>">
                                                    <?php echo $employee['name']; ?> (<?php echo $employee['department']; ?>)
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-link me-2"></i>Kart Ata
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h5><i class="fas fa-id-card me-2"></i>Kayıtlı Kartlar</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Kart ID</th>
                                            <th>Durum</th>
                                            <th>Atanan Personel</th>
                                            <th>Atanma Tarihi</th>
                                            <th>İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($cards)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center">Kayıtlı kart bulunamadı.</td>
                                        </tr>
                                        <?php else: ?>
                                        <?php foreach ($cards as $card): ?>
                                        <tr>
                                            <td><?php echo $card['card_id']; ?></td>
                                            <td>
                                                <?php if ($card['status'] === 'active'): ?>
                                                <span class="badge bg-success">Aktif</span>
                                                <?php else: ?>
                                                <span class="badge bg-secondary">Pasif</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php 
                                                if (!empty($card['employee_id'])) {
                                                    $employeeName = 'Bilinmeyen';
                                                    foreach ($employees as $employee) {
                                                        if ($employee['id'] == $card['employee_id']) {
                                                            $employeeName = $employee['name'];
                                                            break;
                                                        }
                                                    }
                                                    echo $employeeName;
                                                } else {
                                                    echo '<span class="text-muted">Atanmamış</span>';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php 
                                                if (!empty($card['assigned_date'])) {
                                                    echo date('d.m.Y H:i', strtotime($card['assigned_date']));
                                                } else {
                                                    echo '<span class="text-muted">-</span>';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($card['employee_id'])): ?>
                                                <form method="post" action="" class="d-inline">
                                                    <input type="hidden" name="action" value="unassign">
                                                    <input type="hidden" name="card_id" value="<?php echo $card['card_id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-warning" onclick="return confirm('Kart atamasını kaldırmak istediğinize emin misiniz?')">
                                                        <i class="fas fa-unlink"></i> Atamayı Kaldır
                                                    </button>
                                                </form>
                                                <?php endif; ?>
                                                
                                                <form method="post" action="" class="d-inline">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="card_id" value="<?php echo $card['card_id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Kartı silmek istediğinize emin misiniz?')">
                                                        <i class="fas fa-trash"></i> Sil
                                                    </button>
                                                </form>
                                            </td>
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
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>