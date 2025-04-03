<?php
require_once 'config/database.php'; // Veritabanı bağlantısı
require_once 'includes/functions.php'; // Fonksiyonlar

session_start();
checkLogin();

if ($_SERVER["REQUEST_METHOD"] == "POST") { // Form gönderildiğinde çalışacak
    $employee_id = $_SESSION['user_id']; // Çalışan kendi talebini oluşturur
    $overtime_hours = $_POST['overtime_hours']; // Fazla mesai saati
    $overtime_date = $_POST['overtime_date']; // Fazla mesai tarihi

    $stmt = $pdo->prepare("INSERT INTO overtime_requests (employee_id, overtime_hours, overtime_date) VALUES (?, ?, ?)");
    if ($stmt->execute([$employee_id, $overtime_hours, $overtime_date])) {
        echo "Fazla mesai talebiniz gönderildi!";
        header("Location: overtime_requests.php");
        exit;
    } else {
        echo "Talep başarısız!";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Fazla Mesai Talebi</title>
</head>
<body>
    <h2>Fazla Mesai Talebi</h2>
    <form action="request_overtime.php" method="POST">
        <label>Mesai Saati:</label>
        <input type="number" step="0.5" name="overtime_hours" required>

        <label>Tarih:</label>
        <input type="date" name="overtime_date" required>

        <button type="submit">Talep Gönder</button>
    </form>
</body>
</html>
