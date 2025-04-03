<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

session_start();
checkLogin();
checkRole(['ik', 'admin']);

$date = date('Y-m-d');
$stmt = $pdo->query("SELECT * FROM attendance WHERE date = '$date'");
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($records as $record) {
    $start = strtotime($record['check_in']);
    $end = strtotime($record['check_out']);
    $overtime_hours = 0;
    $night_hours = 0;

    // Fazla mesai hesapla (Normal mesai 18:00'de bitiyor)
    if ($end > strtotime("$date 18:00:00")) {
        $overtime_hours = ($end - strtotime("$date 18:00:00")) / 3600;
    }

    // Gece zammı hesapla (22:00 - 06:00)
    if ($end > strtotime("$date 22:00:00")) {
        $night_hours = ($end - strtotime("$date 22:00:00")) / 3600;
    }

    // Veritabanına güncelle
    $stmt = $pdo->prepare("UPDATE attendance SET overtime_hours = ?, night_hours = ? WHERE id = ?");
    $stmt->execute([$overtime_hours, $night_hours, $record['id']]);
}

echo "Fazla mesai ve gece zammı hesaplandı!";
?>