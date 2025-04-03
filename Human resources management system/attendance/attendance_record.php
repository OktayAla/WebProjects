<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

session_start();
checkLogin();
checkRole(['puantor', 'admin']);

// Form gönderildiğinde
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employee_id = $_POST['employee_id'];
    $date = $_POST['date'];
    $check_in = $_POST['check_in'];
    $check_out = $_POST['check_out'];

    // Veritabanına kaydetme işlemleri...
    echo "Kayıt Başarılı!";
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Puantaj Kaydı</title>
</head>
<body>
    <h2>Puantaj Kaydı Ekle/Düzenle</h2>
    <form action="attendance_record.php" method="POST">
        <label>Çalışan ID:</label>
        <input type="number" name="employee_id" required><br>

        <label>Tarih:</label>
        <input type="date" name="date" required><br>

        <label>Giriş Saati:</label>
        <input type="time" name="check_in"><br>

        <label>Çıkış Saati:</label>
        <input type="time" name="check_out"><br>

        <button type="submit">Kaydet</button>
    </form>
</body>
</html>
