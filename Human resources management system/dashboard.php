<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

session_start();
checkLogin();

$user = getUserById($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İK Yönetim Sistemi - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
            <a class="navbar-brand" href="#">İK Yönetim Sistemi</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="dashboard/employees.php">Çalışanlar</a></li>
                    <li class="nav-item"><a class="nav-link" href="dashboard/attendance_list.php">Puantaj</a></li>
                    <li class="nav-item"><a class="nav-link" href="dashboard/leave_requests.php">İzin Talepleri</a></li>
                    <li class="nav-item"><a class="nav-link" href="dashboard/overtime_requests.php">Fazla Mesai</a></li>
                    <li class="nav-item"><a class="nav-link" href="budget/budget_report.php">Bütçe Raporu</a></li>
                </ul>
                <span class="navbar-text">
                    Hoşgeldiniz, <?php echo htmlspecialchars($user['name']); ?> 
                    <a href="logout.php" class="btn btn-outline-light btn-sm ms-2">Çıkış</a>
                </span>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-3">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Toplam Çalışan</h5>
                        <?php
                        $stmt = $pdo->query("SELECT COUNT(*) FROM employees WHERE status='Aktif'");
                        echo "<p class='card-text display-4'>".$stmt->fetchColumn()."</p>";
                        ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card text-white bg-success mb-3">
                    <div class="card-body">
                        <h5 class="card-title">İzin Talepleri</h5>
                        <?php
                        $stmt = $pdo->query("SELECT COUNT(*) FROM leave_requests WHERE status='Beklemede'");
                        echo "<p class='card-text display-4'>".$stmt->fetchColumn()."</p>";
                        ?>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Fazla Mesai</h5>
                        <?php
                        $stmt = $pdo->query("SELECT COUNT(*) FROM overtime_requests WHERE status='Beklemede'");
                        echo "<p class='card-text display-4'>".$stmt->fetchColumn()."</p>";
                        ?>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card text-white bg-info mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Bugün İşte</h5>
                        <?php
                        $stmt = $pdo->query("SELECT COUNT(*) FROM attendance WHERE date=CURRENT_DATE");
                        echo "<p class='card-text display-4'>".$stmt->fetchColumn()."</p>";
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Son İzin Talepleri</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <?php
                            $stmt = $pdo->query("SELECT lr.*, e.name FROM leave_requests lr 
                                               JOIN employees e ON lr.employee_id = e.id 
                                               ORDER BY lr.id DESC LIMIT 5");
                            $requests = $stmt->fetchAll();
                            ?>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Çalışan</th>
                                        <th>Başlangıç</th>
                                        <th>Bitiş</th>
                                        <th>Durum</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($requests as $req): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($req['name']); ?></td>
                                        <td><?php echo $req['start_date']; ?></td>
                                        <td><?php echo $req['end_date']; ?></td>
                                        <td><?php echo $req['status']; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">Son Fazla Mesai Talepleri</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <?php
                            $stmt = $pdo->query("SELECT or.*, e.name FROM overtime_requests or 
                                               JOIN employees e ON or.employee_id = e.id 
                                               ORDER BY or.id DESC LIMIT 5");
                            $overtimes = $stmt->fetchAll();
                            ?>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Çalışan</th>
                                        <th>Tarih</th>
                                        <th>Saat</th>
                                        <th>Durum</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($overtimes as $ot): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($ot['name']); ?></td>
                                        <td><?php echo $ot['overtime_date']; ?></td>
                                        <td><?php echo $ot['overtime_hours']; ?></td>
                                        <td><?php echo $ot['status']; ?></td>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>