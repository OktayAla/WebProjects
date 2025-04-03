<?php
// Veritabanı Bağlantı Bilgileri
$host = 'localhost';
$dbname = 'hrms_db';
$username = 'root'; // Kullanıcı adı
$password = ''; // Parola

// Veritabanına bağlanma
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
// Bağlantı hatası durumunda hata mesajı göster
catch (PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}
?>