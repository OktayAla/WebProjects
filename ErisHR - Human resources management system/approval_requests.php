<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SESSION['role'] !== 'manager' && $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$userId = $_SESSION['user_id'];
$userName = $_SESSION['name'];
$userRole = $_SESSION['role'];
$userDepartment = $_SESSION['department'];

$successMessage = '';
$errorMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $requestType = isset($_POST['request_type']) ? $_POST['request_type'] : '';
    $requestId = isset($_POST['request_id']) ? $_POST['request_id'] : '';

    if ($requestType === 'leave') {
        $leaveRequestsFile = 'data/leave_requests.json';
        $leaveRequests = [];

        if (file_exists($leaveRequestsFile)) {
            $leaveRequests = json_decode(file_get_contents($leaveRequestsFile), true);
            $requestFound = false;

            foreach ($leaveRequests as &$request) {
                if ($request['id'] === $requestId) {
                    if ($userRole === 'manager' && $request['department'] !== $userDepartment) {
                        $errorMessage = "Sadece kendi departmanınızdaki talepleri onaylayabilirsiniz.";
                        break;
                    }

                    $request['status'] = ($action === 'approve') ? 'approved' : 'rejected';
                    $request['approved_by'] = $userId;
                    $request['approved_date'] = date('Y-m-d H:i:s');

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

                            file_put_contents($employeesFile, json_encode($employees, JSON_PRETTY_PRINT));

                            $usersFile = 'data/users.json';
                            if (file_exists($usersFile)) {
                                $users = json_decode(file_get_contents($usersFile), true);

                                foreach ($users as &$user) {
                                    if ($user['id'] == $request['employee_id']) {
                                        $user['remaining_leave'] = $employee['remaining_leave'];
                                        break;
                                    }
                                }

                                file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT));
                            }
                        }
                    }

                    $requestFound = true;
                    break;
                }
            }

            if ($requestFound) {
                file_put_contents($leaveRequestsFile, json_encode($leaveRequests, JSON_PRETTY_PRINT));
                $successMessage = "İzin talebi başarıyla " . (($action === 'approve') ? 'onaylandı' : 'reddedildi') . ".";
            } else {
                $errorMessage = "İzin talebi bulunamadı.";
            }
        }
    } elseif ($requestType === 'advance') {
        $advanceRequestsFile = 'data/advance_requests.json';
        $advanceRequests = [];

        if (file_exists($advanceRequestsFile)) {
            $advanceRequests = json_decode(file_get_contents($advanceRequestsFile), true);
            $requestFound = false;

            foreach ($advanceRequests as &$request) {
                if ($request['id'] === $requestId) {
                    if ($userRole === 'manager' && $request['department'] !== $userDepartment) {
                        $errorMessage = "Sadece kendi departmanınızdaki talepleri onaylayabilirsiniz.";
                        break;
                    }

                    $request['status'] = ($action === 'approve') ? 'approved' : 'rejected';
                    $request['approved_by'] = $userId;
                    $request['approved_date'] = date('Y-m-d H:i:s');

                    if ($action === 'approve') {
                        $paymentDate = new DateTime();
                        $paymentDate->add(new DateInterval('P3D'));
                        $request['payment_date'] = $paymentDate->format('Y-m-d');
                    }

                    $requestFound = true;
                    break;
                }
            }

            if ($requestFound) {
                file_put_contents($advanceRequestsFile, json_encode($advanceRequests, JSON_PRETTY_PRINT));
                $successMessage = "Avans talebi başarıyla " . (($action === 'approve') ? 'onaylandı' : 'reddedildi') . ".";
            } else {
                $errorMessage = "Avans talebi bulunamadı.";
            }
        }
    }
}

$leaveRequestsFile = 'data/leave_requests.json';
$pendingLeaveRequests = [];

if (file_exists($leaveRequestsFile)) {
    $allLeaveRequests = json_decode(file_get_contents($leaveRequestsFile), true);

    foreach ($allLeaveRequests as $request) {
        if ($request['status'] === 'pending' && ($userRole === 'admin' || $request['department'] === $userDepartment)) {
            $pendingLeaveRequests[] = $request;
        }
    }

    usort($pendingLeaveRequests, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
}

$advanceRequestsFile = 'data/advance_requests.json';
$pendingAdvanceRequests = [];

if (file_exists($advanceRequestsFile)) {
    $allAdvanceRequests = json_decode(file_get_contents($advanceRequestsFile), true);

    foreach ($allAdvanceRequests as $request) {
        if ($request['status'] === 'pending' && ($userRole === 'admin' || $request['department'] === $userDepartment)) {
            $pendingAdvanceRequests[] = $request;
        }
    }

    usort($pendingAdvanceRequests, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
}

$totalPendingRequests = count($pendingLeaveRequests) + count($pendingAdvanceRequests);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRMS - Onay Bekleyen Talepler</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="app-container">
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
                    <li><a href="team_management.php"><i class="fas fa-users"></i> Ekip Yönetimi</a></li>
                    <li><a href="approval_requests.php" class="active"><i class="fas fa-tasks"></i> Onay Bekleyen Talepler</a></li>
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

        <main class="main-content">
            <header class="header">
                <div class="header-left">
                    <button id="sidebar-toggle" class="sidebar-toggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h2>Onay Bekleyen Talepler</h2>
                </div>
                <div class="header-right">
                    <div class="notification">
                        <i class="fas fa-bell"></i>
                        <span class="badge"><?php echo $totalPendingRequests; ?></span>
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

                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Onay Bekleyen İzin Talepleri</h5>
                            <span class="badge bg-warning"><?php echo count($pendingLeaveRequests); ?></span>
                        </div>
                        <div class="card-body">
                            <?php if (empty($pendingLeaveRequests)): ?>
                            <div class="alert alert-info">Onay bekleyen izin talebi bulunmamaktadır.</div>
                            <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Personel</th>
                                            <th>Departman</th>
                                            <th>Başlangıç</th>
                                            <th>Bitiş</th>
                                            <th>Gün</th>
                                            <th>Tür</th>
                                            <th>Sebep</th>
                                            <th>Talep Tarihi</th>
                                            <th>İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pendingLeaveRequests as $request): ?>
                                        <tr>
                                            <td><?php echo $request['employee_name']; ?></td>
                                            <td><?php echo $request['department']; ?></td>
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
                                            <td><?php echo $request['reason']; ?></td>
                                            <td><?php echo date('d.m.Y H:i', strtotime($request['created_at'])); ?></td>
                                            <td>
                                                <form action="" method="post" class="d-inline">
                                                    <input type="hidden" name="action" value="approve">
                                                    <input type="hidden" name="request_type" value="leave">
                                                    <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Bu izin talebini onaylamak istediğinizden emin misiniz?')">
                                                        <i class="fas fa-check"></i> Onayla
                                                    </button>
                                                </form>
                                                <form action="" method="post" class="d-inline">
                                                    <input type="hidden" name="action" value="reject">
                                                    <input type="hidden" name="request_type" value="leave">
                                                    <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bu izin talebini reddetmek istediğinizden emin misiniz?')">
                                                        <i class="fas fa-times"></i> Reddet
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Onay Bekleyen Avans Talepleri</h5>
                            <span class="badge bg-warning"><?php echo count($pendingAdvanceRequests); ?></span>
                        </div>
                        <div class="card-body">
                            <?php if (empty($pendingAdvanceRequests)): ?>
                            <div class="alert alert-info">Onay bekleyen avans talebi bulunmamaktadır.</div>
                            <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Personel</th>
                                            <th>Departman</th>
                                            <th>Miktar</th>
                                            <th>Sebep</th>
                                            <th>Talep Tarihi</th>
                                            <th>İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pendingAdvanceRequests as $request): ?>
                                        <tr>
                                            <td><?php echo $request['employee_name']; ?></td>
                                            <td><?php echo $request['department']; ?></td>
                                            <td><?php echo number_format($request['amount'], 2, ',', '.'); ?> ₺</td>
                                            <td><?php echo $request['reason']; ?></td>
                                            <td><?php echo date('d.m.Y H:i', strtotime($request['created_at'])); ?></td>
                                            <td>
                                                <form action="" method="post" class="d-inline">
                                                    <input type="hidden" name="action" value="approve">
                                                    <input type="hidden" name="request_type" value="advance">
                                                    <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Bu avans talebini onaylamak istediğinizden emin misiniz?')">
                                                        <i class="fas fa-check"></i> Onayla
                                                    </button>
                                                </form>
                                                <form action="" method="post" class="d-inline">
                                                    <input type="hidden" name="action" value="reject">
                                                    <input type="hidden" name="request_type" value="advance">
                                                    <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Bu avans talebini reddetmek istediğinizden emin misiniz?')">
                                                        <i class="fas fa-times"></i> Reddet
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>