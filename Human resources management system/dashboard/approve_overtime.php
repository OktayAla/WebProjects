<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

session_start();
checkLogin();
checkRole(['ik', 'admin']);

if (!isset($_GET['id'])) {
    echo "Geçersiz mesai talebi!";
    exit;
}

$overtime_id = $_GET['id'];

$stmt = $pdo->prepare("SELECT overtime_requests.*, employees.name FROM overtime_requests JOIN employees ON overtime_requests.employee_id = employees.id WHERE overtime_requests.id = ?");
$stmt->execute([$overtime_id]);
$overtime = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$overtime) {
    echo "Mesai talebi bulunamadı!";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $status = $_POST['status'];

    $updateStmt = $pdo->prepare("UPDATE overtime_requests SET status = ? WHERE id = ?");
    if ($updateStmt->execute([$status, $overtime_id])) {
        echo "Fazla mesai talebi güncellendi!";
        header("Location: overtime_requests.php");
        exit;
    }
}
?>

<form action="approve_overtime.php?id=<?php echo $overtime_id; ?>" method="POST">
    <label>Durum:</label>
    <select name="status">
        <option value="Onaylandı">Onaylandı</option>
        <option value="Reddedildi">Reddedildi</option>
    </select>
    <button type="submit">Güncelle</button>
</form>
