<?php
require_once 'config/database.php'; // Veritabanı bağlantısı
require_once 'includes/functions.php'; // Fonksiyonlar

session_start();

// Kullanıcı giriş yapmış mı kontrol et
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Kullanıcıyı veritabanında ara
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Giriş bilgilerini kontrol et
    // Eğer kullanıcı varsa ve şifre doğruysa
    // Kullanıcıyı oturum açtır
    if ($user && verifyPassword($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        echo "Giriş başarılı, yönlendiriliyorsunuz...";
        header("Location: dashboard.php"); // Ana sayfaya yönlendir
        exit;
    } else {
        echo "Geçersiz e-posta veya şifre!";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <h2>Giriş Yap</h2>
    <form action="login.php" method="POST">
        <label>E-Posta:</label>
        <input type="email" name="email" required>
        
        <label>Şifre:</label>
        <input type="password" name="password" required>

        <button type="submit">Giriş Yap</button>
    </form>
</body>
</html>
