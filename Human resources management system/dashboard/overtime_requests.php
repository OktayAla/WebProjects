<?php
require_once 'config/database.php'; // Veritabanı bağlantısı
require_once 'includes/functions.php'; // Fonksiyonlar

session_start();
checkLogin();
checkRole(['ik', 'admin']);

$stmt = $pdo->query("SELECT overtime_requests.*, employees.name FROM overtime_requests JOIN employees ON overtime_requests.employee_id = employees.id");
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Fazla Mesai Talepleri</title>
</head>
<body>
    <h2>Fazla Mesai Talepleri</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Çalışan</th>
            <th>Mesai Saati</th>
            <th>Tarih</th>
            <th>Durum</th>
            <th>İşlemler</th>
        </tr>
        <?php foreach ($requests as $request) : ?>
        <tr>
            <td><?php echo $request['id']; ?></td>
            <td><?php echo htmlspecialchars($request['name']); ?></td>
            <td><?php echo $request['overtime_hours']; ?></td>
            <td><?php echo $request['overtime_date']; ?></td>
            <td><?php echo $request['status']; ?></td>
            <td><a href="approve_overtime.php?id=<?php echo $request['id']; ?>">Onayla</a></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>