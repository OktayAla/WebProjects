<?php
session_start();

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    
    // In a real application, we would validate against a database
    // For this demo, we'll use a JSON file to store user credentials
    $usersFile = 'data/users.json';
    
    // Check if users file exists
    if (file_exists($usersFile)) {
        $users = json_decode(file_get_contents($usersFile), true);
        
        // Check if user exists and password matches
        foreach ($users as $user) {
            if ($user['username'] === $username && password_verify($password, $user['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['department'] = $user['department'];
                $_SESSION['remaining_leave'] = $user['remaining_leave'];
                
                // Redirect to dashboard
                header("Location: index.php");
                exit();
            }
        }
    }
    
    // If we get here, authentication failed
    $error = "Kullanıcı adı veya şifre hatalı!";
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ErisHR - Giriş</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <img src="img/logo.png" alt="HRMS Logo" class="login-logo">
                <h2>ErisHR</h2>
                <p>İnsan Kaynakları Yönetim Sistemi</p>
            </div>
            
            <div class="login-body">
                <?php if (isset($error)): ?>
                <div class="error-message animate__animated animate__shakeX">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?php echo $error; ?>
                </div>
                <?php endif; ?>
                
                <form method="post" action="">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="username" name="username" placeholder="Kullanıcı Adı" required>
                        <label for="username"><i class="fas fa-user me-2"></i>Kullanıcı Adı</label>
                    </div>
                    
                    <div class="form-floating">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Şifre" required>
                        <label for="password"><i class="fas fa-lock me-2"></i>Şifre</label>
                    </div>
                    
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" value="" id="rememberMe">
                        <label class="form-check-label" for="rememberMe">
                            Beni Hatırla
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i>Giriş Yap
                    </button>
                </form>
            </div>
            
            <div class="login-footer">
                <p>Copyright © 2025 
                    <br>
                OA Grafik Tasarım tarafından ❤️ ile geliştirilmiştir.</p>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>