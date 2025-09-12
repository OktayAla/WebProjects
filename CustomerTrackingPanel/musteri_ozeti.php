<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
	http_response_code(405);
	echo json_encode(['success' => false, 'message' => 'Method not allowed']);
	exit;
}

$pdo = get_pdo_connection();
$customerId = isset($_GET['customer']) ? (int)$_GET['customer'] : 0;
if ($customerId <= 0) {
	echo json_encode(['success' => false, 'message' => 'Geçersiz müşteri.']);
	exit;
}

// Müşteri ve bakiye
$stmt = $pdo->prepare('SELECT id, isim FROM musteriler WHERE id = ?');
$stmt->execute([$customerId]);
$customer = $stmt->fetch();
if (!$customer) {
	echo json_encode(['success' => false, 'message' => 'Müşteri bulunamadı.']);
	exit;
}

// Toplam borç ve tahsilat
$totals = $pdo->prepare('SELECT 
	COALESCE(SUM(CASE WHEN odeme_tipi = "borc" THEN miktar END),0) AS toplam_borc,
	COALESCE(SUM(CASE WHEN odeme_tipi = "tahsilat" THEN miktar END),0) AS toplam_tahsilat
FROM islemler WHERE musteri_id = ?');
$totals->execute([$customerId]);
$t = $totals->fetch();

$balance = (float)$t['toplam_borc'] - (float)$t['toplam_tahsilat'];

echo json_encode([
	'success' => true,
	'data' => [
		'customer' => $customer,
		'toplam_borc' => (float)$t['toplam_borc'],
		'toplam_tahsilat' => (float)$t['toplam_tahsilat'],
		'bakiye' => $balance
	]
]);


