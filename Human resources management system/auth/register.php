<?php
require_once 'config/database.php'; // Veritabanı bağlantısı
require_once 'includes/functions.php'; // Fonksiyonlar

session_start();

// Token oluştur
$token = generateToken();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['token']) || !validateToken($_POST['token'])) {
        echo "Geçersiz token!";
        exit;
    }
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = $_POST['role'];

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo "Bu e-posta adresi zaten kayıtlı!";
        exit;
    }

    $hashedPassword = hashPassword($password);

    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$name, $email, $hashedPassword, $role])) {
        logAction("Register", "New user registered with email: $email");
        echo "Kayıt başarılı. Giriş yapabilirsiniz.";
        header("Location: login.php");
        exit;
    } else {
        echo "Bir hata oluştu, tekrar deneyin.";
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Ol</title>
</head>
<body>
    <h2>Kayıt Ol</h2>
    <form action="register.php" method="POST">
        <input type="hidden" name="token" value="<?php echo $token; ?>">
        <label>Ad Soyad:</label>
        <input type="text" name="name" required>
        <label>E-Posta:</label>
        <input type="email" name="email" required>
        <label>Şifre:</label>
        <input type="password" name="password" required>
        <label>Rol:</label>
        <select name="role">
            <option value="ik">İnsan Kaynakları</option>
            <option value="puantor">Puantör</option>
            <option value="yonetici">Yönetici</option>
            <option value="admin">Admin</option>
        </select>
        <button type="submit">Kayıt Ol</button>
    </form>
</body>
</html>