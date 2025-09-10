<!-- Bu sistem Oktay ALA tarafından, Analiz Tarım için geliştirilmiştir. -->
<!-- Copyright © Her Hakkı Saklıdır. Ticari amaçlı kullanılması yasaktır. -->
 
<?php 
require_once __DIR__ . '/includes/auth.php';
require_login();

$pdo = get_pdo_connection();
$query = $_GET['q'] ?? '';

if (strlen($query) < 2) {
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare("SELECT id, isim, numara FROM musteriler WHERE isim LIKE ? ORDER BY isim ASC LIMIT 10");
$stmt->execute(["%$query%"]);
$customers = $stmt->fetchAll();

header('Content-Type: application/json');
echo json_encode($customers);
?>
