<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

session_start();
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']); // Şifre direkt olarak alınıyor

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
    $stmt->execute([$email, $password]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = 'Geçersiz e-posta veya şifre!';
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap - İK Yönetim Sistemi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #2c3e50, #3498db);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 0 30px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-header h1 {
            color: #2c3e50;
            font-size: 1.8rem;
            font-weight: 600;
        }
        .form-control {
            padding: 0.8rem 1rem;
            font-size: 1rem;
            border: 2px solid #eee;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        .btn-login {
            width: 100%;
            padding: 0.8rem;
            font-size: 1rem;
            font-weight: 600;
            background: linear-gradient(135deg, #3498db, #2980b9);
            border: none;
            color: white;
            border-radius: 8px;
            margin-top: 1rem;
        }
        .error-message {
            color: #e74c3c;
            background: #fdf0ed;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>İK Yönetim Sistemi</h1>
            <p class="text-muted">Hesabınıza giriş yapın</p>
        </div>

        <?php if ($error): ?>
            <div class="error-message">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="mb-3">
                <label class="form-label">E-Posta Adresi</label>
                <input type="email" name="email" class="form-control" required 
                       placeholder="ornek@sirket.com">
            </div>

            <div class="mb-3">
                <label class="form-label">Şifre</label>
                <input type="password" name="password" class="form-control" required 
                       placeholder="••••••••">
            </div>

            <button type="submit" class="btn btn-login">Giriş Yap</button>
        </form>
    </div>
</body>
</html>
