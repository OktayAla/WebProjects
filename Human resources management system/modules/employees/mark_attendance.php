<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

session_start();
checkLogin();

$employee_id = $_SESSION['user_id'];
$date = date('Y-m-d');

// Çalışanın bugünkü giriş kaydı var mı?
$stmt = $pdo->prepare("SELECT * FROM attendance WHERE employee_id = ? AND date = ?");
$stmt->execute([$employee_id, $date]);
$attendance = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$attendance) {
    // Eğer giriş kaydı yoksa, giriş saati ekle
    $stmt = $pdo->prepare("INSERT INTO attendance (employee_id, date, check_in) VALUES (?, ?, ?)");
    $stmt->execute([$employee_id, $date, date('H:i:s')]);
    echo "Giriş saati kaydedildi!";
} else {
    // Eğer giriş kaydı varsa, çıkış saati ekle
    $stmt = $pdo->prepare("UPDATE attendance SET check_out = ? WHERE id = ?");
    $stmt->execute([date('H:i:s'), $attendance['id']]);
    echo "Çıkış saati kaydedildi!";
}

header("Location: dashboard.php");
exit;
?>
