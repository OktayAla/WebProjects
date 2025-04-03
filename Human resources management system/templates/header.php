<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'İK Yönetim Sistemi'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-gradient fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="/dashboard.php">
                <i class="fas fa-building me-2"></i>
                İK Yönetim Sistemi
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard/employees.php">
                            <i class="fas fa-users me-1"></i> Çalışanlar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard/attendance_list.php">
                            <i class="fas fa-clock me-1"></i> Puantaj
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard/leave_requests.php">
                            <i class="fas fa-calendar-alt me-1"></i> İzin Talepleri
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard/overtime_requests.php">
                            <i class="fas fa-business-time me-1"></i> Fazla Mesai
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/budget/budget_report.php">
                            <i class="fas fa-chart-pie me-1"></i> Bütçe Raporu
                        </a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <div class="dropdown">
                        <button class="btn btn-link nav-link dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i>
                            <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Kullanıcı'); ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/profile.php"><i class="fas fa-id-card me-2"></i>Profil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Çıkış</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="main-content">
        <div class="container-fluid mt-5 pt-4">
