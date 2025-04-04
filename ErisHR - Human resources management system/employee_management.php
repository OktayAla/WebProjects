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
    
    // Load existing employees data
    $employeesFile = 'data/employees.json';
    $employees = [];
    
    if (file_exists($employeesFile)) {
        $employees = json_decode(file_get_contents($employeesFile), true);
    }
    
    // Load existing users data
    $usersFile = 'data/users.json';
    $users = [];
    
    if (file_exists($usersFile)) {
        $users = json_decode(file_get_contents($usersFile), true);
    }
    
    // Process based on action
    if ($action === 'add') {
        // Get form data
        $name = $_POST['name'];
        $department = $_POST['department'];
        $position = $_POST['position'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $hireDate = $_POST['hire_date'];
        $managerId = !empty($_POST['manager_id']) ? (int)$_POST['manager_id'] : null;
        $annualLeave = (int)$_POST['annual_leave'];
        $role = $_POST['role'];
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
        
        // Find the highest employee ID
        $maxId = 0;
        foreach ($employees as $employee) {
            if ($employee['id'] > $maxId) {
                $maxId = $employee['id'];
            }
        }
        
        // Create new employee
        $newEmployeeId = $maxId + 1;
        $newEmployee = [
            'id' => $newEmployeeId,
            'name' => $name,
            'department' => $department,
            'position' => $position,
            'email' => $email,
            'phone' => $phone,
            'hire_date' => $hireDate,
            'manager_id' => $managerId,
            'annual_leave' => $annualLeave,
            'remaining_leave' => $annualLeave,
            'status' => 'active'
        ];
        
        // Create new user
        $newUser = [
            'id' => $newEmployeeId,
            'name' => $name,
            'username' => $username,
            'password' => $password,
            'role' => $role,
            'department' => $department,
            'email' => $email,
            'phone' => $phone,
            'hire_date' => $hireDate,
            'position' => $position,
            'annual_leave' => $annualLeave,
            'remaining_leave' => $annualLeave
        ];
        
        // Add new employee and user
        $employees[] = $newEmployee;
        $users[] = $newUser;
        
        // Save updated data
        file_put_contents($employeesFile, json_encode($employees, JSON_PRETTY_PRINT));
        file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
        
        $successMessage = "Personel başarıyla eklendi.";
    } elseif ($action === 'edit') {
        // Get form data
        $employeeId = (int)$_POST['employee_id'];
        $name = $_POST['name'];
        $department = $_POST['department'];
        $position = $_POST['position'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $hireDate = $_POST['hire_date'];
        $managerId = !empty($_POST['manager_id']) ? (int)$_POST['manager_id'] : null;
        $annualLeave = (int)$_POST['annual_leave'];
        $remainingLeave = (int)$_POST['remaining_leave'];
        $status = $_POST['status'];
        $role = $_POST['role'];
        
        // Update employee data
        $employeeFound = false;
        foreach ($employees as &$employee) {
            if ($employee['id'] === $employeeId) {
                $employee['name'] = $name;
                $employee['department'] = $department;
                $employee['position'] = $position;
                $employee['email'] = $email;
                $employee['phone'] = $phone;
                $employee['hire_date'] = $hireDate;
                $employee['manager_id'] = $managerId;
                $employee['annual_leave'] = $annualLeave;
                $employee['remaining_leave'] = $remainingLeave;
                $employee['status'] = $status;
                $employeeFound = true;
                break;
            }
        }
        
        // Update user data
        $userFound = false;
        foreach ($users as &$user) {
            if ($user['id'] === $employeeId) {
                $user['name'] = $name;
                $user['department'] = $department;
                $user['position'] = $position;
                $user['email'] = $email;
                $user['phone'] = $phone;
                $user['hire_date'] = $hireDate;
                $user['annual_leave'] = $annualLeave;
                $user['remaining_leave'] = $remainingLeave;
                $user['role'] = $role;
                $userFound = true;
                break;
            }
        }
        
        if ($employeeFound && $userFound) {
            // Save updated data
            file_put_contents($employeesFile, json_encode($employees, JSON_PRETTY_PRINT));
            file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
            
            $successMessage = "Personel bilgileri başarıyla güncellendi.";
        } else {
            $errorMessage = "Personel bulunamadı.";
        }
    } elseif ($action === 'delete') {
        $employeeId = (int)$_POST['employee_id'];
        
        // Find employee by ID
        $employeeIndex = -1;
        foreach ($employees as $index => $employee) {
            if ($employee['id'] === $employeeId) {
                $employeeIndex = $index;
                break;
            }
        }
        
        // Find user by ID
        $userIndex = -1;
        foreach ($users as $index => $user) {
            if ($user['id'] === $employeeId) {
                $userIndex = $index;
                break;
            }
        }
        
        if ($employeeIndex >= 0 && $userIndex >= 0) {
            // Remove employee and user
            array_splice($employees, $employeeIndex, 1);
            array_splice($users, $userIndex, 1);
            
            // Save updated data
            file_put_contents($employeesFile, json_encode($employees, JSON_PRETTY_PRINT));
            file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
            
            $successMessage = "Personel başarıyla silindi.";
        } else {
            $errorMessage = "Personel bulunamadı.";
        }
    }
}

// Load employees data for display
$employeesFile = 'data/employees.json';
$employees = [];

if (file_exists($employeesFile)) {
    $employees = json_decode(file_get_contents($employeesFile), true);
    
    // Sort employees by name
    usort($employees, function($a, $b) {
        return strcmp($a['name'], $b['name']);
    });
}

// Load departments for dropdown
$departments = [];
foreach ($employees as $employee) {
    if (!in_array($employee['department'], $departments)) {
        $departments[] = $employee['department'];
    }
}
sort($departments);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRMS - Personel Yönetimi</title>
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
                    <li><a href="employee_management.php" class="active"><i class="fas fa-user-cog"></i> Personel Yönetimi</a></li>
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
                    <h2>Personel Yönetimi</h2>
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
                            <h5 class="mb-0">Personel Listesi</h5>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
                                <i class="fas fa-plus"></i> Yeni Personel Ekle
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Ad Soyad</th>
                                            <th>Departman</th>
                                            <th>Pozisyon</th>
                                            <th>E-posta</th>
                                            <th>Telefon</th>
                                            <th>İşe Başlama</th>
                                            <th>Durum</th>
                                            <th>İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($employees as $employee): ?>
                                        <tr>
                                            <td><?php echo $employee['id']; ?></td>
                                            <td><?php echo $employee['name']; ?></td>
                                            <td><?php echo $employee['department']; ?></td>
                                            <td><?php echo $employee['position']; ?></td>
                                            <td><?php echo $employee['email']; ?></td>
                                            <td><?php echo $employee['phone']; ?></td>
                                            <td><?php echo $employee['hire_date']; ?></td>
                                            <td>
                                                <span class="badge <?php echo ($employee['status'] === 'active') ? 'bg-success' : 'bg-danger'; ?>">
                                                    <?php echo ($employee['status'] === 'active') ? 'Aktif' : 'Pasif'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#viewEmployeeModal" 
                                                    data-id="<?php echo $employee['id']; ?>"
                                                    data-name="<?php echo $employee['name']; ?>"
                                                    data-department="<?php echo $employee['department']; ?>"
                                                    data-position="<?php echo $employee['position']; ?>"
                                                    data-email="<?php echo $employee['email']; ?>"
                                                    data-phone="<?php echo $employee['phone']; ?>"
                                                    data-hire-date="<?php echo $employee['hire_date']; ?>"
                                                    data-manager-id="<?php echo $employee['manager_id']; ?>"
                                                    data-annual-leave="<?php echo $employee['annual_leave']; ?>"
                                                    data-remaining-leave="<?php echo $employee['remaining_leave']; ?>"
                                                    data-status="<?php echo $employee['status']; ?>">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editEmployeeModal"
                                                    data-id="<?php echo $employee['id']; ?>"
                                                    data-name="<?php echo $employee['name']; ?>"
                                                    data-department="<?php echo $employee['department']; ?>"
                                                    data-position="<?php echo $employee['position']; ?>"
                                                    data-email="<?php echo $employee['email']; ?>"
                                                    data-phone="<?php echo $employee['phone']; ?>"
                                                    data-hire-date="<?php echo $employee['hire_date']; ?>"
                                                    data-manager-id="<?php echo $employee['manager_id']; ?>"
                                                    data-annual-leave="<?php echo $employee['annual_leave']; ?>"
                                                    data-remaining-leave="<?php echo $employee['remaining_leave']; ?>"
                                                    data-status="<?php echo $employee['status']; ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteEmployeeModal" data-id="<?php echo $employee['id']; ?>" data-name="<?php echo $employee['name']; ?>">
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
    
    <!-- Add Employee Modal -->
    <div class="modal fade" id="addEmployeeModal" tabindex="-1" aria-labelledby="addEmployeeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addEmployeeModalLabel">Yeni Personel Ekle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Ad Soyad</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="department" class="form-label">Departman</label>
                                <select class="form-select" id="department" name="department" required>
                                    <?php foreach ($departments as $dept): ?>
                                    <option value="<?php echo $dept; ?>"><?php echo $dept; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="position" class="form-label">Pozisyon</label>
                                <input type="text" class="form-control" id="position" name="position" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">E-posta</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Telefon</label>
                                <input type="text" class="form-control" id="phone" name="phone" required>
                            </div>
                            <div class="col-md-6">
                                <label for="hire_date" class="form-label">İşe Başlama Tarihi</label>
                                <input type="date" class="form-control" id="hire_date" name="hire_date" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="manager_id" class="form-label">Yönetici</label>
                                <select class="form-select" id="manager_id" name="manager_id">
                                    <option value="">Yönetici Seçin</option>
                                    <?php foreach ($employees as $manager): ?>
                                    <?php if ($manager['role'] === 'manager' || $manager['role'] === 'admin'): ?>
                                    <option value="<?php echo $manager['id']; ?>"><?php echo $manager['name']; ?></option>
                                    <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="annual_leave" class="form-label">Yıllık İzin Hakkı</label>
                                <input type="number" class="form-control" id="annual_leave" name="annual_leave" min="0" value="14" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="role" class="form-label">Rol</label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="employee">Personel</option>
                                    <option value="manager">Yönetici</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                        </div>
                        
                        <hr>
                        <h6>Giriş Bilgileri</h6>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="username" class="form-label">Kullanıcı Adı</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="col-md-6">
                                <label for="password" class="form-label">Şifre</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
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
                            <p><strong>Durum:</strong> <span id="view-status"></span></p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Edit Employee Modal -->
    <div class="modal fade" id="editEmployeeModal" tabindex="-1" aria-labelledby="editEmployeeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editEmployeeModalLabel">Personel Düzenle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" id="edit-employee-id" name="employee_id">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit-name" class="form-label">Ad Soyad</label>
                                <input type="text" class="form-control" id="edit-name" name="name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="edit-department" class="form-label">Departman</label>
                                <select class="form-select" id="edit-department" name="department" required>
                                    <?php foreach ($departments as $dept): ?>
                                    <option value="<?php echo $dept; ?>"><?php echo $dept; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit-position" class="form-label">Pozisyon</label>
                                <input type="text" class="form-control" id="edit-position" name="position" required>
                            </div>
                            <div class="col-md-6">
                                <label for="edit-email" class="form-label">E-posta</label>
                                <input type="email" class="form-control" id="edit-email" name="email" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit-phone" class="form-label">Telefon</label>
                                <input type="text" class="form-control" id="edit-phone" name="phone" required>
                            </div>
                            <div class="col-md-6">
                                <label for="edit-hire-date" class="form-label">İşe Başlama Tarihi</label>
                                <input type="date" class="form-control" id="edit-hire-date" name="hire_date" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit-manager-id" class="form-label">Yönetici</label>
                                <select class="form-select" id="edit-manager-id" name="manager_id">
                                    <option value="">Yönetici Seçin</option>
                                    <?php foreach ($employees as $manager): ?>
                                    <?php if ($manager['role'] === 'manager' || $manager['role'] === 'admin'): ?>
                                    <option value="<?php echo $manager['id']; ?>"><?php echo $manager['name']; ?></option>
                                    <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="edit-annual-leave" class="form-label">Yıllık İzin Hakkı</label>
                                <input type="number" class="form-control" id="edit-annual-leave" name="annual_leave" min="0" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit-remaining-leave" class="form-label">Kalan İzin</label>
                                <input type="number" class="form-control" id="edit-remaining-leave" name="remaining_leave" min="0" required>
                            </div>
                            <div class="col-md-6">
                                <label for="edit-status" class="form-label">Durum</label>
                                <select class="form-select" id="edit-status" name="status" required>
                                    <option value="active">Aktif</option>
                                    <option value="inactive">Pasif</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="edit-role" class="form-label">Rol</label>
                                <select class="form-select" id="edit-role" name="role" required>
                                    <option value="employee">Personel</option>
                                    <option value="manager">Yönetici</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
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
    
    <!-- Delete Employee Modal -->
    <div class="modal fade" id="deleteEmployeeModal" tabindex="-1" aria-labelledby="deleteEmployeeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteEmployeeModalLabel">Personel Sil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" id="delete-employee-id" name="employee_id">
                        <p>Bu personeli silmek istediğinizden emin misiniz?</p>
                        <p><strong>Personel:</strong> <span id="delete-employee-name"></span></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                        <button type="submit" class="btn btn-danger">Sil</button>
                    </div>
                </form>
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
                const status = this.getAttribute('data-status');
                
                document.getElementById('view-id').textContent = id;
                document.getElementById('view-name').textContent = name;
                document.getElementById('view-department').textContent = department;
                document.getElementById('view-position').textContent = position;
                document.getElementById('view-email').textContent = email;
                document.getElementById('view-phone').textContent = phone;
                document.getElementById('view-hire-date').textContent = hireDate;
                document.getElementById('view-annual-leave').textContent = annualLeave;
                document.getElementById('view-remaining-leave').textContent = remainingLeave;
                document.getElementById('view-status').textContent = status === 'active' ? 'Aktif' : 'Pasif';
            });
        });
        
        // Edit Employee Modal
        document.querySelectorAll('[data-bs-target="#editEmployeeModal"]').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                const department = this.getAttribute('data-department');
                const position = this.getAttribute('data-position');
                const email = this.getAttribute('data-email');
                const phone = this.getAttribute('data-phone');
                const hireDate = this.getAttribute('data-hire-date');
                const managerId = this.getAttribute('data-manager-id');
                const annualLeave = this.getAttribute('data-annual-leave');
                const remainingLeave = this.getAttribute('data-remaining-leave');
                const status = this.getAttribute('data-status');
                
                document.getElementById('edit-employee-id').value = id;
                document.getElementById('edit-name').value = name;
                document.getElementById('edit-department').value = department;
                document.getElementById('edit-position').value = position;
                document.getElementById('edit-email').value = email;
                document.getElementById('edit-phone').value = phone;
                document.getElementById('edit-hire-date').value = hireDate;
                document.getElementById('edit-manager-id').value = managerId || '';
                document.getElementById('edit-annual-leave').value = annualLeave;
                document.getElementById('edit-remaining-leave').value = remainingLeave;
                document.getElementById('edit-status').value = status;
            });
        });
        
        // Delete Employee Modal
        document.querySelectorAll('[data-bs-target="#deleteEmployeeModal"]').forEach(button => {
            button.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const name = this.getAttribute('data-name');
                
                document.getElementById('delete-employee-id').value = id;
                document.getElementById('delete-employee-name').textContent = name;
            });
        });
    </script>
</body>
</html>