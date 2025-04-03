<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

session_start();
checkLogin();
checkRole(['ik', 'admin']);

$stmt = $pdo->query("SELECT a.*, 
                            CONCAT(e.first_name, ' ', e.last_name) as employee_name,
                            d.name as department_name
                     FROM attendance a 
                     LEFT JOIN employees e ON a.employee_id = e.id 
                     LEFT JOIN departments d ON e.department_id = d.id
                     ORDER BY a.date DESC, a.check_in DESC");
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
            <td><?php echo htmlspecialchars($record['employee_name']); ?></td>
            <td><?php echo $record['date']; ?></td>
            <td><?php echo $record['check_in'] ?? '-'; ?></td>
            <td><?php echo $record['check_out'] ?? '-'; ?></td>
            <td><?php echo $record['status']; ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
