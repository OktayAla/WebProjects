<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$userName = $_SESSION['name'];
$userRole = $_SESSION['role'];
$userDepartment = $_SESSION['department'];

$scanSuccess = false;
$scanMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'scan_card') {
    $cardId = $_POST['card_id'];

    $cardsFile = 'data/cards.json';
    $cards = [];

    if (file_exists($cardsFile)) {
        $cards = json_decode(file_get_contents($cardsFile), true);
    }

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

        $now = new DateTime();
        $date = $now->format('Y-m-d');
        $time = $now->format('H:i:s');

        $attendanceFile = 'data/attendance.json';
        $attendance = [];

        if (file_exists($attendanceFile)) {
            $attendance = json_decode(file_get_contents($attendanceFile), true);
        }

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

        $recordType = 'entry';
        if ($hasEntry && !$hasExit) {
            $recordType = 'exit';
        } elseif ($hasEntry && $hasExit) {
            $hour = (int)$now->format('H');
            if ($hour < 13) {
                $recordType = 'entry';
            } else {
                $recordType = 'exit';
            }
        }

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

        $attendance[] = $newRecord;

        file_put_contents($attendanceFile, json_encode($attendance, JSON_PRETTY_PRINT));

        $scanSuccess = true;
        $scanMessage = "$employeeName için $recordType kaydı oluşturuldu. Saat: $time";

        $employeeAttendanceDir = 'js/users';
        if (!file_exists($employeeAttendanceDir)) {
            mkdir($employeeAttendanceDir, 0777, true);
        }

        $employeeAttendanceFile = "$employeeAttendanceDir/$employeeId.js";

        $employeeRecords = [];
        foreach ($attendance as $record) {
            if ($record['employee_id'] == $employeeId) {
                $employeeRecords[] = $record;
            }
        }

        $jsContent = json_encode($employeeRecords, JSON_PRETTY_PRINT);
        file_put_contents($employeeAttendanceFile, $jsContent);
    }
}

$attendanceFile = 'data/attendance.json';
$attendanceRecords = [];
$userAttendanceRecords = [];

if (file_exists($attendanceFile)) {
    $allRecords = json_decode(file_get_contents($attendanceFile), true);

    if ($userRole === 'admin') {
        $attendanceRecords = $allRecords;
    } elseif ($userRole === 'manager') {
        foreach ($allRecords as $record) {
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
        foreach ($allRecords as $record) {
            if ($record['employee_id'] == $userId) {
                $attendanceRecords[] = $record;
            }
        }
    }

    usort($attendanceRecords, function($a, $b) {
        return strtotime($b['timestamp']) - strtotime($a['timestamp']);
    });

    foreach ($attendanceRecords as $record) {
        if ($record['employee_id'] == $userId) {
            $date = $record['date'];

            if ($record['type'] === 'entry') {
                if (!isset($userAttendanceRecords[$date])) {
                    $userAttendanceRecords[$date] = [];
                }
                $userAttendanceRecords[$date]['entry'] = $record['time'];
            } elseif ($record['type'] === 'exit') {
                if (!isset($userAttendanceRecords[$date])) {
                    $userAttendanceRecords[$date] = [];
                }
                $userAttendanceRecords[$date]['exit'] = $record['time'];
            }
        }
    }
}

// Team attendance processing (moved here for clarity and avoid re-reading files)
$teamAttendanceRecords = [];
if ($userRole === 'manager' || $userRole === 'admin') {
    $allEmployees = [];
    $employeesFile = 'data/employees.json';
    if (file_exists($employeesFile)) {
        $allEmployees = json_decode(file_get_contents($employeesFile), true);
    }

    $employeeMap = [];
    foreach ($allEmployees as $emp) {
        $employeeMap[$emp['id']] = $emp;
    }

    $teamRecordsProcessed = [];
    $tempTeamAttendance = [];

    if (file_exists($attendanceFile)) {
        $allRecords = json_decode(file_get_contents($attendanceFile), true);

        foreach ($allRecords as $record) {
            $employeeId = $record['employee_id'];
            $employeeData = $employeeMap[$employeeId] ?? null;

            if ($employeeData) {
                $employeeDepartment = $employeeData['department'];
                $shouldInclude = false;

                if ($userRole === 'admin') {
                    $shouldInclude = true;
                } elseif ($userRole === 'manager' && $employeeDepartment === $userDepartment) {
                    $shouldInclude = true;
                }

                if ($shouldInclude) {
                    $date = $record['date'];
                    $key = $employeeId . '_' . $date;

                    if (!isset($tempTeamAttendance[$key])) {
                        $tempTeamAttendance[$key] = [
                            'employee_id' => $employeeId,
                            'employee_name' => $record['employee_name'],
                            'date' => $date,
                            'entry' => null,
                            'exit' => null,
                            'timestamp' => $record['timestamp'] // Use latest timestamp for sorting
                        ];
                    }

                    if ($record['type'] === 'entry' && (!isset($tempTeamAttendance[$key]['entry']) || strtotime($record['time']) < strtotime($tempTeamAttendance[$key]['entry']))) {
                       $tempTeamAttendance[$key]['entry'] = $record['time'];
                    } elseif ($record['type'] === 'exit' && (!isset($tempTeamAttendance[$key]['exit']) || strtotime($record['time']) > strtotime($tempTeamAttendance[$key]['exit']))) {
                       $tempTeamAttendance[$key]['exit'] = $record['time'];
                    }
                     // Update timestamp if this record is later
                    if(strtotime($record['timestamp']) > strtotime($tempTeamAttendance[$key]['timestamp'])){
                         $tempTeamAttendance[$key]['timestamp'] = $record['timestamp'];
                    }
                }
            }
        }
    }

    $teamAttendanceRecords = array_values($tempTeamAttendance);

    // Sort team records by date and then employee name
    usort($teamAttendanceRecords, function($a, $b) {
         $dateComparison = strtotime($b['date']) - strtotime($a['date']);
        if ($dateComparison !== 0) {
            return $dateComparison;
        }
        return strcmp($a['employee_name'], $b['employee_name']);
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

                    <?php if($userRole == 'manager' || $userRole == 'admin'): ?>
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
                <div class="container-fluid">
                    <?php if (!empty($scanMessage) && !$scanSuccess): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                         <?php echo $scanMessage; ?>
                         <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php elseif (!empty($scanMessage) && $scanSuccess): ?>
                     <div class="alert alert-success alert-dismissible fade show" role="alert">
                         <?php echo $scanMessage; ?>
                         <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif; ?>


                    <?php if ($userRole === 'manager' || $userRole === 'admin'): ?>
                    <ul class="nav nav-tabs mb-4" id="attendanceTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal" type="button" role="tab" aria-controls="personal" aria-selected="true">
                                <i class="fas fa-user"></i> Kişisel Giriş/Çıkış Kayıtları
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="team-tab" data-bs-toggle="tab" data-bs-target="#team" type="button" role="tab" aria-controls="team" aria-selected="false">
                                <i class="fas fa-users"></i> Ekip Giriş/Çıkış Kayıtları
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="attendanceTabsContent">
                        <div class="tab-pane fade show active" id="personal" role="tabpanel" aria-labelledby="personal-tab">
                    <?php endif; ?>

                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="mb-0">PDKS Kart Okutma Simülasyonu</h5>
                        </div>
                        <div class="card-body">
                            <form action="" method="post" class="row g-3">
                                <input type="hidden" name="action" value="scan_card">
                                <div class="col-md-6">
                                    <label for="card_id" class="form-label">Kart ID</label>
                                    <input type="text" class="form-control" id="card_id" name="card_id" required>
                                </div>
                                <div class="col-md-6 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-id-card"></i> Kart Okut
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                             <h5 class="mb-0"><?php echo ($userRole === 'manager' || $userRole === 'admin') ? 'Kişisel Kayıtlar' : 'Giriş/Çıkış Kayıtları'; ?></h5>
                             <div>
                                <input type="text" id="personalDateFilter" class="form-control form-control-sm d-inline-block w-auto" placeholder="Tarihe Göre Filtrele">
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="clearPersonalFilter">Filtreyi Temizle</button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover" id="attendanceTable">
                                    <thead>
                                        <tr>
                                            <th>Tarih</th>
                                            <th>Giriş Saati</th>
                                            <th>Çıkış Saati</th>
                                            <th>Toplam Süre</th>
                                            <th>Durum</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Sort userAttendanceRecords by date descending
                                         uksort($userAttendanceRecords, function ($a, $b) {
                                            return strtotime($b) - strtotime($a);
                                        });
                                        ?>
                                        <?php foreach ($userAttendanceRecords as $date => $record): ?>
                                        <tr data-date="<?php echo $date; ?>">
                                            <td><?php echo date('d.m.Y', strtotime($date)); ?></td>
                                            <td>
                                                <?php
                                                if (isset($record['entry'])) {
                                                    echo date('H:i', strtotime($record['entry']));

                                                    $entryTime = strtotime($record['entry']);
                                                    $startTime = strtotime('09:00:00');

                                                    if ($entryTime > $startTime) {
                                                        echo ' <span class="badge bg-warning text-dark">Geç Giriş</span>';
                                                    }
                                                } else {
                                                    echo '-';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                if (isset($record['exit'])) {
                                                    echo date('H:i', strtotime($record['exit']));

                                                    $exitTime = strtotime($record['exit']);
                                                    $endTime = strtotime('18:00:00');

                                                    if ($exitTime < $endTime) {
                                                        echo ' <span class="badge bg-info text-dark">Erken Çıkış</span>';
                                                    }
                                                } else {
                                                    echo '-';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                if (isset($record['entry']) && isset($record['exit'])) {
                                                    $entry = new DateTime($record['entry']);
                                                    $exit = new DateTime($record['exit']);
                                                    $interval = $entry->diff($exit);
                                                    echo $interval->format('%H sa %I dk');
                                                } else {
                                                    echo '-';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                if (!isset($record['entry']) && !isset($record['exit'])) {
                                                     echo '<span class="badge bg-danger">Giriş/Çıkış Yok</span>';
                                                } elseif (isset($record['entry']) && isset($record['exit'])) {
                                                    echo '<span class="badge bg-success">Tamamlandı</span>';
                                                } elseif (isset($record['entry'])) {
                                                    echo '<span class="badge bg-primary">Giriş Yapıldı</span>';
                                                } elseif (isset($record['exit'])) {
                                                    echo '<span class="badge bg-secondary">Sadece Çıkış</span>'; // Should not normally happen
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                         <?php if (empty($userAttendanceRecords)): ?>
                                            <tr>
                                                <td colspan="5" class="text-center">Kişisel giriş/çıkış kaydı bulunmamaktadır.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <?php if ($userRole === 'manager' || $userRole === 'admin'): ?>
                        </div>
                        <div class="tab-pane fade" id="team" role="tabpanel" aria-labelledby="team-tab">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Ekip Giriş/Çıkış Kayıtları</h5>
                                    <div>
                                        <input type="text" id="teamDateFilter" class="form-control form-control-sm d-inline-block w-auto" placeholder="Tarihe Göre Filtrele">
                                        <select id="teamEmployeeFilter" class="form-select form-select-sm d-inline-block w-auto">
                                             <option value="">Tüm Personeller</option>
                                             <?php
                                                $displayedEmployees = [];
                                                foreach ($teamAttendanceRecords as $rec) {
                                                    if (!in_array($rec['employee_id'], $displayedEmployees)) {
                                                         echo '<option value="' . $rec['employee_id'] . '">' . $rec['employee_name'] . '</option>';
                                                         $displayedEmployees[] = $rec['employee_id'];
                                                    }
                                                }
                                             ?>
                                        </select>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="clearTeamFilter">Filtreyi Temizle</button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover" id="teamAttendanceTable">
                                            <thead>
                                                <tr>
                                                    <th>Personel</th>
                                                    <th>Tarih</th>
                                                    <th>Giriş Saati</th>
                                                    <th>Çıkış Saati</th>
                                                    <th>Toplam Süre</th>
                                                    <th>Durum</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($teamAttendanceRecords as $record): ?>
                                                <tr data-date="<?php echo $record['date']; ?>" data-employee="<?php echo $record['employee_id']; ?>">
                                                    <td><?php echo $record['employee_name']; ?></td>
                                                    <td><?php echo date('d.m.Y', strtotime($record['date'])); ?></td>
                                                    <td>
                                                        <?php
                                                        if (isset($record['entry'])) {
                                                            echo date('H:i', strtotime($record['entry']));

                                                            $entryTime = strtotime($record['entry']);
                                                            $startTime = strtotime('09:00:00');

                                                            if ($entryTime > $startTime) {
                                                                 echo ' <span class="badge bg-warning text-dark">Geç Giriş</span>';
                                                            }
                                                        } else {
                                                            echo '-';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        if (isset($record['exit'])) {
                                                            echo date('H:i', strtotime($record['exit']));

                                                            $exitTime = strtotime($record['exit']);
                                                            $endTime = strtotime('18:00:00');

                                                            if ($exitTime < $endTime) {
                                                                 echo ' <span class="badge bg-info text-dark">Erken Çıkış</span>';
                                                            }
                                                        } else {
                                                            echo '-';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        if (isset($record['entry']) && isset($record['exit'])) {
                                                            $entry = new DateTime($record['entry']);
                                                            $exit = new DateTime($record['exit']);
                                                            $interval = $entry->diff($exit);
                                                             echo $interval->format('%H sa %I dk');
                                                        } else {
                                                            echo '-';
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                         <?php
                                                        if (!isset($record['entry']) && !isset($record['exit'])) {
                                                             echo '<span class="badge bg-danger">Giriş/Çıkış Yok</span>';
                                                        } elseif (isset($record['entry']) && isset($record['exit'])) {
                                                            echo '<span class="badge bg-success">Tamamlandı</span>';
                                                        } elseif (isset($record['entry'])) {
                                                            echo '<span class="badge bg-primary">Giriş Yapıldı</span>';
                                                        } elseif (isset($record['exit'])) {
                                                            echo '<span class="badge bg-secondary">Sadece Çıkış</span>'; // Should not normally happen
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                                <?php if (empty($teamAttendanceRecords)): ?>
                                                    <tr>
                                                        <td colspan="6" class="text-center">Ekip giriş/çıkış kaydı bulunmamaktadır.</td>
                                                    </tr>
                                                <?php endif; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/tr.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="js/main.js"></script>
    <script src="js/attendance.js"></script>
</body>
</html>