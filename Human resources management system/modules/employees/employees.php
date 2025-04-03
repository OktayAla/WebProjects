<?php
require_once 'config/database.php'; // Veritabanı bağlantısı
require_once 'includes/functions.php'; // Fonksiyonlar

session_start();
checkLogin();

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];

// Eğer yönetici ise, sadece kendi şirketindeki çalışanları görebilir
if ($role === 'yonetici') {
    $stmt = $pdo->prepare("SELECT * FROM employees WHERE company_id = (SELECT company_id FROM users WHERE id = ?)");
    $stmt->execute([$user_id]);
} else {
    $stmt = $pdo->query("SELECT * FROM employees");
}

// Eğer İK veya admin ise, tüm çalışanları görebilir
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Çalışanlar</title>
</head>
<body>
    <h2>Çalışan Listesi</h2>
    <table border="1">
        <tr>
            <th>ID</th>
            <th>Şirket ID</th>
            <th>Ad</th>
            <th>E-Posta</th>
            <th>Pozisyon</th>
            <th>Maaş</th>
            <th>Durum</th>
            <th>İşlemler</th>
        </tr>
        <?php foreach ($employees as $employee) : ?>
        <tr>
            <td><?php echo $employee['id']; ?></td>
            <td><?php echo $employee['company_id']; ?></td>
            <td><?php echo htmlspecialchars($employee['name']); ?></td>
            <td><?php echo htmlspecialchars($employee['email']); ?></td>
            <td><?php echo htmlspecialchars($employee['position']); ?></td>
            <td><?php echo $employee['salary']; ?> ₺</td>
            <td><?php echo $employee['status']; ?></td>
            <td>
                <a href="edit_employee.php?id=<?php echo $employee['id']; ?>">Düzenle</a> | 
                <a href="delete_employee.php?id=<?php echo $employee['id']; ?>" onclick="return confirm('Silmek istediğinize emin misiniz?');">Sil</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
