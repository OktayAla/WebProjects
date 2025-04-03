<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

session_start();
checkLogin();
checkRole(['puantor', 'admin', 'ik']);

$month = isset($_GET['month']) ? $_GET['month'] : date('Y-m');

$stmt = $pdo->prepare("SELECT a.*, e.name 
                      FROM attendance a 
                      JOIN employees e ON a.employee_id = e.id 
                      WHERE DATE_FORMAT(a.date, '%Y-%m') = ?
                      ORDER BY a.date DESC");
$stmt->execute([$month]);
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Aylık Puantaj Raporu</title>
</head>
<body>
    <h2>Aylık Puantaj Raporu - <?php echo $month; ?></h2>
    
    <form method="GET">
        <input type="month" name="month" value="<?php echo $month; ?>">
        <button type="submit">Filtrele</button>
    </form>

    <table border="1">
        <tr>
            <th>Tarih</th>
            <th>Çalışan</th>
            <th>Giriş</th>
            <th>Çıkış</th>
            <th>Toplam Saat</th>
        </tr>
        <?php foreach($records as $record): ?>
            <tr>
                <td><?php echo $record['date']; ?></td>
                <td><?php echo htmlspecialchars($record['name']); ?></td>
                <td><?php echo $record['check_in']; ?></td>
                <td><?php echo $record['check_out']; ?></td>
                <td><?php 
                    $hours = (strtotime($record['check_out']) - strtotime($record['check_in'])) / 3600;
                    echo number_format($hours, 2);
                ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
