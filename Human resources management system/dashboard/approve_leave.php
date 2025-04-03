<?php
require_once 'config/database.php'; // Veritabanı bağlantısı
require_once 'includes/functions.php'; // Fonksiyonlar

session_start();
checkLogin();
checkRole(['ik', 'admin']); // Sadece İK ve admin onay verebilir

if (!isset($_GET['id'])) {
    echo "Geçersiz izin talebi!";
    exit;
}

$leave_id = $_GET['id'];

// İzin talebini çek
$stmt = $pdo->prepare("SELECT leave_requests.*, employees.name FROM leave_requests JOIN employees ON leave_requests.employee_id = employees.id WHERE leave_requests.id = ?");
$stmt->execute([$leave_id]);
$leave = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$leave) {
    echo "İzin talebi bulunamadı!";
    exit;
}

// İzin onaylama/reddetme işlemi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $status = $_POST['status'];

    $updateStmt = $pdo->prepare("UPDATE leave_requests SET status = ? WHERE id = ?");
    if ($updateStmt->execute([$status, $leave_id])) {
        echo "İzin talebi güncellendi!";
        header("Location: leave_requests.php");
        exit;
    } else {
        echo "İzin talebi güncellenemedi!";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>İzin Onaylama</title>
</head>
<body>
    <h2><?php echo htmlspecialchars($leave['name']); ?> - İzin Talebi</h2>
    <p>İzin Türü: <?php echo $leave['leave_type']; ?></p>
    <p>Başlangıç: <?php echo $leave['start_date']; ?></p>
    <p>Bitiş: <?php echo $leave['end_date']; ?></p>

    <form action="approve_leave.php?id=<?php echo $leave_id; ?>" method="POST">
        <label>Durum:</label>
        <select name="status">
            <option value="Onaylandı">Onaylandı</option>
            <option value="Reddedildi">Reddedildi</option>
        </select>
        <button type="submit">Güncelle</button>
    </form>

    <a href="leave_requests.php">Geri Dön</a>
</body>
</html>
