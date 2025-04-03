<?php
require_once '../config/database.php';
require_once '../includes/functions.php';

session_start();
checkLogin();
checkRole(['admin']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $settings = [
        'system_email' => $_POST['system_email'],
        'notification_enabled' => isset($_POST['notification_enabled']) ? 1 : 0,
        'maintenance_mode' => isset($_POST['maintenance_mode']) ? 1 : 0
    ];
    
    foreach ($settings as $key => $value) {
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) 
                             VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->execute([$key, $value, $value]);
    }
}

// Mevcut ayarları getir
$stmt = $pdo->query("SELECT * FROM settings");
$current_settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Sistem Ayarları</title>
</head>
<body>
    <h2>Sistem Ayarları</h2>
    <form method="POST">
        <label>Sistem E-posta Adresi:</label>
        <input type="email" name="system_email" 
               value="<?php echo $current_settings['system_email'] ?? ''; ?>"><br>

        <label>Bildirimler Aktif:</label>
        <input type="checkbox" name="notification_enabled" 
               <?php echo ($current_settings['notification_enabled'] ?? '') ? 'checked' : ''; ?>><br>

        <label>Bakım Modu:</label>
        <input type="checkbox" name="maintenance_mode" 
               <?php echo ($current_settings['maintenance_mode'] ?? '') ? 'checked' : ''; ?>><br>

        <button type="submit">Kaydet</button>
    </form>
</body>
</html>
