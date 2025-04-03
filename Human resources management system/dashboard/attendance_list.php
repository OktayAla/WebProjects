<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

session_start();
checkLogin();
checkRole(['ik', 'admin']);

$stmt = $pdo->query("SELECT attendance.*, employees.name FROM attendance JOIN employees ON attendance.employee_id = employees.id ORDER BY date DESC");
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Puantaj Listesi</title>
</head>
<body>
    <h2>Puantaj Listesi</h2>
    <table border="1">
        <tr>
            <th>Çalışan</th>
            <th>Tarih</th>
            <th>Giriş</th>
            <th>Çıkış</th>
            <th>Durum</th>
        </tr>
        <?php foreach ($records as $record) : ?>
        <tr>
            <td><?php echo htmlspecialchars($record['name']); ?></td>
            <td><?php echo $record['date']; ?></td>
            <td><?php echo $record['check_in'] ?? '-'; ?></td>
            <td><?php echo $record['check_out'] ?? '-'; ?></td>
            <td><?php echo $record['status']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
