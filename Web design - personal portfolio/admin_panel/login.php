<?php
// login.php - Admin Giriş Sayfası
// Bu dosya, admin paneline giriş yapmak için kullanılan bir PHP dosyasıdır.
session_start();

// Eğer kullanıcı giriş yapmışsa, admin paneline yönlendir
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit;
}

// Eğer giriş formu gönderilmişse, kullanıcı adı ve şifreyi kontrol et
// ve giriş yap
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Kullanıcı adı ve şifreyi kontrol et
    $auth_data = json_decode(file_get_contents('auth.json'), true);
    $users = $auth_data['users'];

    // Eğer kullanıcı adı ve Şifre dogruysa, admin paneline yonlendir
    foreach ($users as $user) {
        if ($user['username'] === $username && $user['password'] === md5($password)) {
            $_SESSION['admin_logged_in'] = true;
            header('Location: index.php');
            exit;
        }
    }
    $error = 'Geçersiz kullanıcı adı veya şifre!';
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Paneli - Giriş</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/admin-style.css">
</head>
<body class="dark-theme login-page">
    <div class="container">
        <div class="login-container">
            <div class="login-box">
                <h2 class="text-center mb-4">Admin Paneli</h2>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <form method="POST" action="">
                    <div class="mb-3">
                        <input type="text" class="form-control" name="username" placeholder="Kullanıcı Adı" required>
                    </div>
                    <div class="mb-3">
                        <input type="password" class="form-control" name="password" placeholder="Şifre" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Giriş Yap</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
