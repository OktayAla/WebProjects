<?php 
require_once __DIR__ . '/includes/auth.php';
require_login();

$pdo = get_pdo_connection();
$query = $_GET['q'] ?? '';

if (strlen($query) < 2) {
    echo json_encode([]);
    exit;
}

// Fiyat alanını ekle (eğer yoksa)
try {
    $pdo->exec("ALTER TABLE urunler ADD COLUMN IF NOT EXISTS fiyat DECIMAL(10,2) DEFAULT 0.00");
} catch (Exception $e) {
    // Alan zaten varsa hata vermez
}

$stmt = $pdo->prepare("SELECT id, isim, fiyat FROM urunler WHERE LOWER(isim) LIKE LOWER(?) ORDER BY isim ASC LIMIT 10");
$stmt->execute(["%$query%"]);
$products = $stmt->fetchAll();

header('Content-Type: application/json');
echo json_encode($products);
?>
