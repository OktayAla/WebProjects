<?php
require_once 'config/database.php'; // Veritabanı bağlantısı
require_once 'includes/functions.php'; // Fonksiyonlar

session_start();
checkLogin();

// Kullanıcı bilgilerini al
$user = getUserById($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel</title>
</head>
<body>
    <h2>Hoşgeldiniz, <?php echo htmlspecialchars($user['name']); ?>!</h2>
    <p>Rolünüz: <?php echo htmlspecialchars($user['role']); ?></p>
    
    <a href="logout.php">Çıkış Yap</a>
</body>
</html>
