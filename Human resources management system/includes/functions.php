<?php
require_once __DIR__ . '/../config/database.php';

function checkLogin() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}

// Şifre doğrulama - hash yerine direkt karşılaştırma
function verifyPassword($password, $stored_password) {
    return $password === $stored_password;
}

// Kullanıcı bilgilerini çekme
function getUserById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function checkRole($allowed_roles) {
    if(!isset($_SESSION['role']) || !in_array($_SESSION['role'], $allowed_roles)) {
        header("Location: error.php?msg=unauthorized");
        exit;
    }
}

function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

// Güncellenmiş generateToken ve validateToken fonksiyonları
function generateToken() {
    if(empty($_SESSION['token'])){
       $_SESSION['token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['token'];
}

function validateToken($token) {
    return isset($_SESSION['token']) && hash_equals($_SESSION['token'], $token);
}

// Yeni loglama fonksiyonu
function logAction($action, $details = '') {
    $logFile = __DIR__ . '/../logs/actions.log';
    $date = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$date] $action: $details" . PHP_EOL, FILE_APPEND);
}
?>