<?php
require_once 'config/database.php'; // Veritabanı bağlantısı
require_once 'includes/functions.php'; // Fonksiyonlar

session_start();
checkLogin();
checkRole(['ik', 'admin']); // Sadece İK ve Admin rolüne sahip kullanıcılar çalışan ekleyebilir

$token = generateToken();

// Kullanıcı bilgilerini al
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['token']) || !validateToken($_POST['token'])) {
        echo "Geçersiz token!";
        exit;
    }
    $company_id = $_POST['company_id'];
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $position = trim($_POST['position']);
    $salary = $_POST['salary'];
    $hire_date = $_POST['hire_date'];
    $status = $_POST['status'];

    // Çalışanı veritabanına ekle
    $stmt = $pdo->prepare("INSERT INTO employees (company_id, name, email, phone, position, salary, hire_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

    // Ekleme işleminden sonra kullanıcıyı employees.php sayfasına yönlendir
    if ($stmt->execute([$company_id, $name, $email, $phone, $position, $salary, $hire_date, $status])) {
        logAction("Add Employee", "New employee added by user ".$_SESSION['user_id']);
        echo "Çalışan başarıyla eklendi!";
        header("Location: employees.php");
        exit;
    } else {
        echo "Bir hata oluştu!";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <title>Çalışan Ekle</title>
</head>

<body>
    <h2>Yeni Çalışan Ekle</h2>
    <form action="add_employee.php" method="POST">
        <input type="hidden" name="token" value="<?php echo $token; ?>">
        <label>Şirket ID:</label>
        <input type="number" name="company_id" required>

        <label>Ad Soyad:</label>
        <input type="text" name="name" required>

        <label>E-Posta:</label>
        <input type="email" name="email" required>

        <label>Telefon:</label>
        <input type="text" name="phone">

        <label>Pozisyon:</label>
        <input type="text" name="position">

        <label>Maaş:</label>
        <input type="number" step="0.01" name="salary">

        <label>İşe Giriş Tarihi:</label>
        <input type="date" name="hire_date">

        <label>Durum:</label>
        <select name="status">
            <option value="Aktif">Aktif</option>
            <option value="Pasif">Pasif</option>
        </select>

        <button type="submit">Ekle</button>
    </form>
</body>

</html>