<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

session_start();
checkLogin();
checkRole(['admin']);

// Şirket bilgilerini güncelleme işlemleri...

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Şirket Ayarları</title>
</head>
<body>
    <h2>Şirket Ayarları</h2>
    <form action="company_settings.php" method="POST">
        <label>Şirket Adı:</label>
        <input type="text" name="company_name"><br>

        <label>Adres:</label>
        <textarea name="address"></textarea><br>

        <!-- Diğer ayar alanları -->

        <button type="submit">Kaydet</button>
    </form>
</body>
</html>
