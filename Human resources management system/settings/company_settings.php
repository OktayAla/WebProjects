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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
