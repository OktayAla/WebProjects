<?php
require_once 'config/database.php'; // Veritabanı bağlantısı
require_once 'includes/functions.php'; // Fonksiyonlar

session_start();
checkLogin();
checkRole(['ik', 'admin']);

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("DELETE FROM employees WHERE id = ?");
    if ($stmt->execute([$_GET['id']])) {
        echo "Çalışan silindi!";
        header("Location: employees.php");
        exit;
    } else {
        echo "Silme işlemi başarısız!";
    }
}
?>
