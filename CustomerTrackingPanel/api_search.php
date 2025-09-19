<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/manual_products.php';
require_login();

$pdo = get_pdo_connection();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$customerId = isset($_GET['customer']) ? (int)$_GET['customer'] : 0;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$productFilter = isset($_GET['product']) ? (int)$_GET['product'] : 0;
$typeFilter = isset($_GET['type']) ? $_GET['type'] : '';
$dateFrom = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$dateTo = isset($_GET['date_to']) ? $_GET['date_to'] : '';

$conditions = [];
$params = [];

if ($customerId) {
    $conditions[] = 'i.musteri_id = ?';
    $params[] = $customerId;
}

if ($search) {
    $conditions[] = '(LOWER(m.isim) LIKE LOWER(?) OR LOWER(i.aciklama) LIKE LOWER(?))';
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($productFilter) {
    $conditions[] = 'i.urun_id = ?';
    $params[] = $productFilter;
}

if ($typeFilter) {
    $conditions[] = 'i.odeme_tipi = ?';
    $params[] = $typeFilter;
}

if ($dateFrom) {
    $conditions[] = 'i.olusturma_zamani >= ?';
    $params[] = $dateFrom . ' 00:00:00';
}

if ($dateTo) {
    $conditions[] = 'i.olusturma_zamani <= ?';
    $params[] = $dateTo . ' 23:59:59';
}

try {
    $whereClause = '';
    if (!empty($conditions)) {
        $whereClause = 'WHERE ' . implode(' AND ', $conditions);
    }

    $countSql = "SELECT COUNT(*) FROM islemler i JOIN musteriler m ON m.id = i.musteri_id WHERE (i.is_main_transaction = 1 OR i.is_main_transaction IS NULL) $whereClause";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $totalRows = (int)$countStmt->fetchColumn();
    $totalPages = max(1, (int)ceil($totalRows / $perPage));

    $safeLimit = (int)$perPage;
    $safeOffset = (int)$offset;

    $sql = "SELECT i.*, m.isim AS musteri_isim, u.isim AS urun_isim, k.isim AS kullanici_isim,
                   (SELECT COUNT(*) FROM islemler sub WHERE sub.parent_transaction_id = i.id) as product_count
           FROM islemler i
           JOIN musteriler m ON m.id = i.musteri_id
           LEFT JOIN urunler u ON u.id = i.urun_id
           LEFT JOIN kullanicilar k ON k.id = i.kullanici_id
           WHERE (i.is_main_transaction = 1 OR i.is_main_transaction IS NULL)
           $whereClause
           ORDER BY i.olusturma_zamani DESC
           LIMIT $safeLimit OFFSET $safeOffset";

    $stmt = $pdo->prepare($sql);
    $paramIndex = 1;

    foreach ($params as $param) {
        $stmt->bindValue($paramIndex++, $param);
    }

    $stmt->execute();
    $transactions = $stmt->fetchAll();
    
    // Manuel ürün adlarını JSON'dan ekle
    foreach ($transactions as &$transaction) {
        if (!$transaction['urun_isim'] && $transaction['urun_id'] === null) {
            $manualName = getManualProduct($transaction['id']);
            if ($manualName) {
                $transaction['new_product_name'] = $manualName;
            }
        }
    }
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Arama sorgusu çalıştırılırken bir hata oluştu.',
    ]);
    exit;
}

$selectedCustomer = null;
if ($customerId) {
    $st = $pdo->prepare('SELECT * FROM musteriler WHERE id = ?');
    $st->execute([$customerId]);
    $selectedCustomer = $st->fetch();
}

$response = [
    'success' => true,
    'data' => [
        'transactions' => $transactions,
        'pagination' => [
            'current_page' => $page,
            'total_pages' => $totalPages,
            'total_rows' => $totalRows,
            'per_page' => $perPage
        ],
        'filters' => [
            'search' => $search,
            'product' => $productFilter,
            'type' => $typeFilter,
            'date_from' => $dateFrom,
            'date_to' => $dateTo
        ],
        'selected_customer' => $selectedCustomer
    ]
];

header('Content-Type: application/json');
echo json_encode($response);
?>
