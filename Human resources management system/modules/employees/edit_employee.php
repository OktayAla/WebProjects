<?php
require_once 'config/database.php'; // Veritabanı bağlantısı
require_once 'includes/functions.php'; // Fonksiyonlar

session_start();
checkLogin();
checkRole(['ik', 'admin']); // Sadece İnsan Kaynakları ve Admin düzenleyebilir

if (!isset($_GET['id'])) {
    echo "Çalışan ID'si bulunamadı!";
    exit;
}

// Çalışan ID'sini al
$employee_id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM employees WHERE id = ?"); // Çalışanı ID ile al
$stmt->execute([$employee_id]); 
$employee = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$employee) {
    echo "Çalışan bulunamadı!";
    exit;
}

// Güncelleme işlemi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $position = trim($_POST['position']);
    $salary = $_POST['salary'];
    $hire_date = $_POST['hire_date'];
    $status = $_POST['status'];

    // Çalışan bilgilerini güncelle
    $updateStmt = $pdo->prepare("UPDATE employees SET name=?, email=?, phone=?, position=?, salary=?, hire_date=?, status=? WHERE id=?");
    if ($updateStmt->execute([$name, $email, $phone, $position, $salary, $hire_date, $status, $employee_id])) {
        echo "Çalışan bilgileri güncellendi!";
        header("Location: employees.php");
        exit;
    } else {
        echo "Güncelleme başarısız!";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Çalışan Düzenle</title>
</head>
<body>
    <h2>Çalışan Bilgilerini Düzenle</h2>
    <form action="edit_employee.php?id=<?php echo $employee_id; ?>" method="POST">
        <label>Ad Soyad:</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($employee['name']); ?>" required>
        
        <label>E-Posta:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($employee['email']); ?>" required>

        <label>Telefon:</label>
        <input type="text" name="phone" value="<?php echo htmlspecialchars($employee['phone']); ?>">

        <label>Pozisyon:</label>
        <input type="text" name="position" value="<?php echo htmlspecialchars($employee['position']); ?>">

        <label>Maaş:</label>
        <input type="number" step="0.01" name="salary" value="<?php echo $employee['salary']; ?>">

        <label>İşe Giriş Tarihi:</label>
        <input type="date" name="hire_date" value="<?php echo $employee['hire_date']; ?>">

        <label>Durum:</label>
        <select name="status">
            <option value="Aktif" <?php if ($employee['status'] == 'Aktif') echo 'selected'; ?>>Aktif</option>
            <option value="Pasif" <?php if ($employee['status'] == 'Pasif') echo 'selected'; ?>>Pasif</option>
        </select>

        <button type="submit">Güncelle</button>
    </form>
</body>
</html>
