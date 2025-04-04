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
$successMessage = '';
$errorMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    
    // Load existing employees data to extract departments
    $employeesFile = 'data/employees.json';
    $employees = [];
    
    if (file_exists($employeesFile)) {
        $employees = json_decode(file_get_contents($employeesFile), true);
    }
    
    // Get unique departments
    $departments = [];
    foreach ($employees as $employee) {
        if (!isset($departments[$employee['department']])) {
            $departments[$employee['department']] = [
                'name' => $employee['department'],
                'manager_id' => null,
                'employee_count' => 0
            ];
        }
        
        // Count employees in department
        $departments[$employee['department']]['employee_count']++;
        
        // Find department manager
        if ($employee['position'] && strpos(strtolower($employee['position']), 'müdür') !== false) {
            $departments[$employee['department']]['manager_id'] = $employee['id'];
            $departments[$employee['department']]['manager_name'] = $employee['name'];
        }
    }
    
    // Process based on action
    if ($action === 'add') {
        $departmentName = $_POST['department_name'];
        $managerId = !empty($_POST['manager_id']) ? (int)$_POST['manager_id'] : null;
        
        // Check if department already exists
        if (isset($departments[$departmentName])) {
            $errorMessage = "Bu departman zaten mevcut.";
        } else {
            // Add new department by updating employees
            if ($managerId) {
                // Find manager and update their department
                foreach ($employees as &$employee) {
                    if ($employee['id'] === $managerId) {
                        $employee['department'] = $departmentName;
                        break;
                    }
                }
                
                // Save updated employees data
                file_put_contents($employeesFile, json_encode($employees, JSON_PRETTY_PRINT));
                
                // Update users data as well
                $usersFile = 'data/users.json';
                if (file_exists($usersFile)) {
                    $users = json_decode(file_get_contents($usersFile), true);
                    
                    foreach ($users as &$user) {
                        if ($user['id'] === $managerId) {
                            $user['department'] = $departmentName;
                            break;
                        }
                    }
                    
                    file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
                }
                
                $successMessage = "Departman başarıyla eklendi.";
            } else {
                $errorMessage = "Departman için bir yönetici seçmelisiniz.";
            }
        }
    } elseif ($action === 'edit') {
        $oldDepartmentName = $_POST['old_department_name'];
        $newDepartmentName = $_POST['department_name'];
        $managerId = !empty($_POST['manager_id']) ? (int)$_POST['manager_id'] : null;
        
        // Check if new department name already exists (unless it's the same as old name)
        if ($oldDepartmentName !== $newDepartmentName && isset($departments[$newDepartmentName])) {
            $errorMessage = "Bu departman adı zaten mevcut.";
        } else {
            // Update department name for all employees in that department
            $updated = false;
            
            foreach ($employees as &$employee) {
                if ($employee['department'] === $oldDepartmentName) {
                    $employee['department'] = $newDepartmentName;
                    $updated = true;
                }
                
                // Update manager if specified
                if ($managerId && $employee['id'] === $managerId) {
                    $employee['department'] = $newDepartmentName;
                }
            }
            
            if ($updated) {
                // Save updated employees data
                file_put_contents($employeesFile, json_encode($employees, JSON_PRETTY_PRINT));
                
                // Update users data as well
                $usersFile = 'data/users.json';
                if (file_exists($usersFile)) {
                    $users = json_decode(file_get_contents($usersFile), true);
                    
                    foreach ($users as &$user) {
                        if ($user['department'] === $oldDepartmentName) {
                            $user['department'] = $newDepartmentName;
                        }
                    }
                    
                    file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
                }
                
                $successMessage = "Departman başarıyla güncellendi.";
            } else {
                $errorMessage = "Departman bulunamadı.";
            }
        }
    } elseif ($action === 'delete') {
        $departmentName = $_POST['department_name'];
        
        // Check if department has employees
        $hasEmployees = false;
        foreach ($employees as $employee) {
            if ($employee['department'] === $departmentName) {
                $hasEmployees = true;
                break;
            }
        }
        
        if ($hasEmployees) {
            $errorMessage = "Bu departmanda çalışanlar var. Önce çalışanları başka departmanlara aktarın.";
        } else {
            $successMessage = "Departman başarıyla silindi.";
        }
    }
    
    // Reload departments after changes
    $departments = [];
    foreach ($employees as $employee) {
        if (!isset($departments[$employee['department']])) {
            $departments[$employee['department']] = [
                'name' => $employee['department'],
                'manager_id' => null,
                'employee_count' => 0
            ];
        }
        
        // Count employees in department
        $departments[$employee['department']]['employee_count']++;
        
        // Find department manager
        if ($employee['position'] && strpos(strtolower($employee['position']), 'müdür') !== false) {
            $departments[$employee['department']]['manager_id'] = $employee['id'];
            $departments[$employee['department']]['manager_name'] = $employee['name'];
        }
    }
}

// Load employees data for display
$employeesFile = 'data/employees.json';
$employees = [];
$departments = [];

if (file_exists($employeesFile)) {
    $employees = json_decode(file_get_contents($employeesFile), true);
    
    // Get unique departments and count employees
    foreach ($employees as $employee) {
        if (!isset($departments[$employee['department']])) {
            $departments[$employee['department']] = [
                'name' => $employee['department'],
                'manager_id' => null,
                'manager_name' => 'Belirtilmemiş',
                'employee_count' => 0
            ];
        }
        
        // Count employees in department
        $departments[$employee['department']]['employee_count']++;
        
        // Find department manager
        if ($employee['position'] && strpos(strtolower($employee['position']), 'müdür') !== false) {
            $departments[$employee['department']]['manager_id'] = $employee['id'];
            $departments[$employee['department']]['manager_name'] = $employee['name'];
        }
    }
    
    // Sort departments by name
    ksort($departments);
}

// Get managers for dropdown
$managers = [];
foreach ($employees as $employee) {
    // Check if role key exists before accessing it
    if (isset($employee['role']) && ($employee['role'] === 'manager' || $employee['role'] === 'admin')) {
        $managers[] = $employee;
    } else if (isset($employee['position']) && (strpos(strtolower($employee['position']), 'müdür') !== false || strpos(strtolower($employee['position']), 'yönetici') !== false)) {
        // If role is not set, use position as a fallback to identify managers
        $managers[] = $employee;
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRMS - Departman Yönetimi</title>
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
                    <li><a href="department_management.php" class="active"><i class="fas fa-building"></i> Departman Yönetimi</a></li>
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
                    <h2>Departman Yönetimi</h2>
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
                    
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Departman Listesi</h5>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addDepartmentModal">
                                <i class="fas fa-plus"></i> Yeni Departman Ekle
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Departman Adı</th>
                                            <th>Departman Yöneticisi</th>
                                            <th>Çalışan Sayısı</th>
                                            <th>İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($departments as $dept): ?>
                                        <tr>
                                            <td><?php echo $dept['name']; ?></td>
                                            <td><?php echo $dept['manager_name'] ?? 'Belirtilmemiş'; ?></td>
                                            <td><?php echo $dept['employee_count']; ?></td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editDepartmentModal" 
                                                    data-name="<?php echo $dept['name']; ?>"
                                                    data-manager-id="<?php echo $dept['manager_id']; ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteDepartmentModal" 
                                                    data-name="<?php echo $dept['name']; ?>"
                                                    data-count="<?php echo $dept['employee_count']; ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
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
        </main>
    </div>
    
    <!-- Add Department Modal -->
    <div class="modal fade" id="addDepartmentModal" tabindex="-1" aria-labelledby="addDepartmentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addDepartmentModalLabel">Yeni Departman Ekle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        
                        <div class="mb-3">
                            <label for="department_name" class="form-label">Departman Adı</label>
                            <input type="text" class="form-control" id="department_name" name="department_name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="manager_id" class="form-label">Departman Yöneticisi</label>
                            <select class="form-select" id="manager_id" name="manager_id" required>
                                <option value="">Yönetici Seçin</option>
                                <?php foreach ($managers as $manager): ?>
                                <option value="<?php echo $manager['id']; ?>"><?php echo $manager['name']; ?> (<?php echo $manager['position']; ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                        <button type="submit" class="btn btn-primary">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Edit Department Modal -->
    <div class="modal fade" id="editDepartmentModal" tabindex="-1" aria-labelledby="editDepartmentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editDepartmentModalLabel">Departman Düzenle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" id="old_department_name" name="old_department_name">
                        
                        <div class="mb-3">
                            <label for="edit_department_name" class="form-label">Departman Adı</label>
                            <input type="text" class="form-control" id="edit_department_name" name="department_name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_manager_id" class="form-label">Departman Yöneticisi</label>
                            <select class="form-select" id="edit_manager_id" name="manager_id" required>
                                <option value="">Yönetici Seçin</option>
                                <?php foreach ($managers as $manager): ?>
                                <option value="<?php echo $manager['id']; ?>"><?php echo $manager['name']; ?> (<?php echo $manager['position']; ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                        <button type="submit" class="btn btn-primary">Kaydet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Delete Department Modal -->
    <div class="modal fade" id="deleteDepartmentModal" tabindex="-1" aria-labelledby="deleteDepartmentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteDepartmentModalLabel">Departman Sil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" id="delete_department_name" name="department_name">
                        <p>Bu departmanı silmek istediğinizden emin misiniz?</p>
                        <p><strong>Departman:</strong> <span id="delete-department-name"></span></p>
                        <p><strong>Çalışan Sayısı:</strong> <span id="delete-employee-count"></span></p>
                        <div class="alert alert-warning" id="delete-warning" style="display: none;">
                            Bu departmanda çalışanlar var. Önce çalışanları başka departmanlara aktarın veya silin.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                        <button type="submit" class="btn btn-danger" id="delete-confirm-btn">Sil</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/main.js"></script>
    <script>
        // Edit Department Modal
        document.querySelectorAll('[data-bs-target="#editDepartmentModal"]').forEach(button => {
            button.addEventListener('click', function() {
                const name = this.getAttribute('data-name');
                const managerId = this.getAttribute('data-manager-id');
                
                document.getElementById('old_department_name').value = name;
                document.getElementById('edit_department_name').value = name;
                document.getElementById('edit_manager_id').value = managerId || '';
            });
        });
        
        // Delete Department Modal
        document.querySelectorAll('[data-bs-target="#deleteDepartmentModal"]').forEach(button => {
            button.addEventListener('click', function() {
                const name = this.getAttribute('data-name');
                const count = parseInt(this.getAttribute('data-count'));
                
                document.getElementById('delete_department_name').value = name;
                document.getElementById('delete-department-name').textContent = name;
                document.getElementById('delete-employee-count').textContent = count;
                
                // Show warning and disable delete button if department has employees
                if (count > 0) {
                    document.getElementById('delete-warning').style.display = 'block';
                    document.getElementById('delete-confirm-btn').disabled = true;
                } else {
                    document.getElementById('delete-warning').style.display = 'none';
                    document.getElementById('delete-confirm-btn').disabled = false;
                }
            });
        });
    </script>
</body>
</html>