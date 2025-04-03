<?php
require_once '../../config/database.php';
require_once '../../includes/functions.php';

session_start();
checkLogin();

if (!isset($_GET['id'])) {
    echo "Çalışan ID'si belirtilmemiş!";
    exit;
}

$employee_id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM employees WHERE id = ?");
$stmt->execute([$employee_id]);
$employee = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$employee) {
    echo "Çalışan bulunamadı!";
    exit;
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Çalışan Detayları</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../../assets/css/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <h2><?php echo htmlspecialchars($employee['name']); ?> - Detaylar</h2>
    <p><strong>E-Posta:</strong> <?php echo htmlspecialchars($employee['email']); ?></p>
    <p><strong>Telefon:</strong> <?php echo htmlspecialchars($employee['phone']); ?></p>
    <p><strong>Pozisyon:</strong> <?php echo htmlspecialchars($employee['position']); ?></p>
    <p><strong>Maaş:</strong> <?php echo $employee['salary']; ?> ₺</p>
    <p><strong>İşe Giriş Tarihi:</strong> <?php echo $employee['hire_date']; ?></p>
    <p><strong>Durum:</strong> <?php echo $employee['status']; ?></p>
    <!-- ...ek detaylar... -->
    <p><a href="list_employees.php">Listeye Dön</a></p>
</body>
</html>
