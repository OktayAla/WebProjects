<?php 
require_once __DIR__ . '/includes/auth.php';
require_login();

$pdo = get_pdo_connection();
$query = $_GET['q'] ?? '';

if (strlen($query) < 2) {
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare("SELECT id, isim, numara FROM musteriler WHERE LOWER(isim) LIKE LOWER(?) ORDER BY isim ASC LIMIT 10");
$stmt->execute(["%$query%"]);
$customers = $stmt->fetchAll();

header('Content-Type: application/json');
echo json_encode($customers);
?>
