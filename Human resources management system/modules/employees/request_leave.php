<?php
require_once 'config/database.php'; // Veritabanı bağlantısı
require_once 'includes/functions.php'; // Fonksiyonlar

session_start();
checkLogin();

$token = generateToken();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['token']) || !validateToken($_POST['token'])) {
        echo "Geçersiz token!";
        exit;
    }
    $employee_id = $_SESSION['user_id']; // Çalışan kendi talebini oluşturur
    $leave_type = $_POST['leave_type']; // İzin türü
    $start_date = $_POST['start_date']; // Başlangıç tarihi
    $end_date = $_POST['end_date']; // Bitiş tarihi

    $stmt = $pdo->prepare("INSERT INTO leave_requests (employee_id, leave_type, start_date, end_date) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$employee_id, $leave_type, $start_date, $end_date])) {
        logAction("Leave Request", "Employee $employee_id requested leave from $start_date to $end_date");
        echo "İzin talebiniz gönderildi!";
        header("Location: leave_requests.php");
        exit;
    } else {
        echo "Talep gönderme başarısız!";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>İzin Talebi</title>
</head>
<body>
    <h2>İzin Talep Formu</h2>
    <form action="request_leave.php" method="POST">
        <input type="hidden" name="token" value="<?php echo $token; ?>">
        <label>İzin Türü:</label>
        <select name="leave_type">
            <option value="Yıllık İzin">Yıllık İzin</option>
            <option value="Raporlu">Raporlu</option>
            <option value="Ücretsiz İzin">Ücretsiz İzin</option>
            <option value="Doğum İzni">Doğum İzni</option>
            <option value="Mazeret İzni">Mazeret İzni</option>
        </select>

        <label>Başlangıç Tarihi:</label>
        <input type="date" name="start_date" required>

        <label>Bitiş Tarihi:</label>
        <input type="date" name="end_date" required>

        <button type="submit">Talep Gönder</button>
    </form>
</body>
</html>
