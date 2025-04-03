<?php
require_once __DIR__ . '/../config/database.php';

// Kullanıcı giriş yapmış mı kontrolünü sağlama
function checkLogin() {
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}

// Şifreyi hashleyerek güvenli hale getirme
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT);
}

// Şifreyi doğrulama
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Kullanıcı bilgilerini çekme
function getUserById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
?>