<!-- Bu sistem Oktay ALA tarafından, Analiz Tarım için geliştirilmiştir. -->
<!-- Copyright © Her Hakkı Saklıdır. Ticari amaçlı kullanılması yasaktır. -->
 
<?php
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/manual_products.php';
require_login();

$pdo = get_pdo_connection();
$customerId = isset($_GET['customer']) ? (int)$_GET['customer'] : 0;

// Mevcut kullanıcı bilgilerini session'dan al
$currentUserId = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : 0;

// Mevcut kullanıcının adını al
$currentUserName = 'Sistem';
if ($currentUserId) {
    $currentUserName = isset($_SESSION['user']['name']) ? $_SESSION['user']['name'] : 'Sistem';
}

$editTransaction = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT i.*, m.isim as musteri_isim, k.isim as kullanici_isim FROM islemler i JOIN musteriler m ON i.musteri_id = m.id LEFT JOIN kullanicilar k ON i.kullanici_id = k.id WHERE i.id = ?');
    $stmt->execute([(int)$_GET['edit']]);
    $editTransaction = $stmt->fetch();
}

// Silme işlemi
if (isset($_GET['delete'])) {
    $transactionId = (int)$_GET['delete'];

    $pdo->beginTransaction();
    try {
        // Önce işlemi alalım (bakiye güncellemesi için)
        $stmt = $pdo->prepare('SELECT * FROM islemler WHERE id = ?');
        $stmt->execute([$transactionId]);
        $transaction = $stmt->fetch();

        if ($transaction) {
            // Müşteri bakiyesini güncelle (silinen işlemi geri al)
            if ($transaction['odeme_tipi'] === 'borc') {
                $pdo->prepare('UPDATE musteriler SET tutar = tutar - ? WHERE id = ?')->execute([$transaction['miktar'], $transaction['musteri_id']]);
            } else {
                $pdo->prepare('UPDATE musteriler SET tutar = tutar + ? WHERE id = ?')->execute([$transaction['miktar'], $transaction['musteri_id']]);
            }

            // İşlemi sil
            $pdo->prepare('DELETE FROM islemler WHERE id = ?')->execute([$transactionId]);

            $pdo->commit();

            // Başarılı mesajı ile yönlendir
            $redirect_url = 'islemler.php';
            $query_params = [];
            if ($customerId) {
                $query_params['customer'] = $customerId;
            }
            if (isset($_GET['page'])) {
                $query_params['page'] = $_GET['page'];
            }
            $query_params['success'] = '3'; // 3 = silme başarılı
            $redirect_url .= '?' . http_build_query($query_params);
            header('Location: ' . $redirect_url);
            exit;
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = 'Silme işlemi başarısız: ' . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo->beginTransaction();
    try {
// İşlem güncelleme - user_id güncellenmez, sadece diğer alanlar güncellenir
            if (isset($_POST['action']) && $_POST['action'] === 'update_transaction' && isset($_POST['transaction_id'])) {
                $transaction_id = (int)$_POST['transaction_id'];
                $urun_id = !empty($_POST['product_id']) ? (int)$_POST['product_id'] : null;
                $odeme_tipi = $_POST['type'];
                $miktar = (float)str_replace([',', ' '], ['.', ''], $_POST['amount']);
                $aciklama = trim($_POST['note']);
                $adet = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;            // Bakiye güncellemesi için eski işlemi alalım
            $oldTxStmt = $pdo->prepare('SELECT i.*, k.isim AS kullanici_isim FROM islemler i LEFT JOIN kullanicilar k ON k.id = i.kullanici_id WHERE i.id = ?');
            $oldTxStmt->execute([$transaction_id]);
            $oldTransaction = $oldTxStmt->fetch();

            if ($oldTransaction) {
                // Eski işlemi bakiyeden çıkar
                if ($oldTransaction['odeme_tipi'] === 'borc') {
                    $pdo->prepare('UPDATE musteriler SET tutar = tutar - ? WHERE id = ?')->execute([$oldTransaction['miktar'], $oldTransaction['musteri_id']]);
                } else {
                    $pdo->prepare('UPDATE musteriler SET tutar = tutar + ? WHERE id = ?')->execute([$oldTransaction['miktar'], $oldTransaction['musteri_id']]);
                }

                // İşlemi güncelle (user_id korunur)
                $stmt = $pdo->prepare('UPDATE islemler SET urun_id = ?, odeme_tipi = ?, miktar = ?, aciklama = ? WHERE id = ?');
                $stmt->execute([$urun_id, $odeme_tipi, $miktar, $aciklama, $transaction_id]);

                // Yeni işlemi bakiyeye ekle
                if ($odeme_tipi === 'borc') {
                    $pdo->prepare('UPDATE musteriler SET tutar = tutar + ? WHERE id = ?')->execute([$miktar, $oldTransaction['musteri_id']]);
                } else {
                    $pdo->prepare('UPDATE musteriler SET tutar = tutar - ? WHERE id = ?')->execute([$miktar, $oldTransaction['musteri_id']]);
                }
            }

            $pdo->commit();
            $redirect_url = 'islemler.php';
            $query_params = [];
            if ($customerId) {
                $query_params['customer'] = $customerId;
            }
            if (isset($_GET['page'])) {
                $query_params['page'] = $_GET['page'];
            }
            $query_params['success'] = '2';
            $redirect_url .= '?' . http_build_query($query_params);
            header('Location: ' . $redirect_url);
            exit;
        }
        // Yeni işlem ekleme (çoklu ürün desteği)
        else {
            // Müşteri ID'sini al veya yeni müşteri oluştur
            $musteri_id = (int)$_POST['customer_id'];
            
            // Eğer müşteri ID 0 ise, yeni müşteri oluştur
            if ($musteri_id === 0 && !empty($_POST['new_customer_name'])) {
                $newCustomerName = trim($_POST['new_customer_name']);
                $newCustomerPhone = trim($_POST['new_customer_phone'] ?? '');
                
                $stmt = $pdo->prepare('INSERT INTO musteriler (isim, numara, tutar) VALUES (?, ?, 0)');
                $stmt->execute([$newCustomerName, $newCustomerPhone]);
                $musteri_id = $pdo->lastInsertId();
            }

            // Çoklu ürün işleme - tek işlem ID'si ile
            $products = $_POST['products'] ?? [];
            $totalDebit = 0;
            $totalCredit = 0;
            $mainTransactionId = null;
            $transactionDescription = '';

            // Önce ana işlem kaydını oluştur
            if (!empty($products)) {
                $firstProduct = $products[0];
                $odeme_tipi = $firstProduct['type'];
                
                // Ürün sayısını hesapla
                $productCount = count($products);
                $transactionDescription = $productCount > 1 ? 
                    "Çoklu ürün işlemi ({$productCount} kalem)" : 
                    "Tek ürün işlemi";
                
                // Ana işlem kaydını oluştur
                try {
                    $stmt = $pdo->prepare('INSERT INTO islemler (musteri_id, urun_id, odeme_tipi, miktar, aciklama, kullanici_id, is_main_transaction) VALUES (?, ?, ?, ?, ?, ?, 1)');
                    $stmt->execute([$musteri_id, null, $odeme_tipi, 0, $transactionDescription, $currentUserId]);
                } catch (Exception $e) {
                    // Eğer yeni sütunlar yoksa, eski formatla ekle
                    if (strpos($e->getMessage(), 'is_main_transaction') !== false) {
                        $stmt = $pdo->prepare('INSERT INTO islemler (musteri_id, urun_id, odeme_tipi, miktar, aciklama, kullanici_id) VALUES (?, ?, ?, ?, ?, ?)');
                        $stmt->execute([$musteri_id, null, $odeme_tipi, 0, $transactionDescription, $currentUserId]);
                    } else {
                        throw $e;
                    }
                }
                $mainTransactionId = $pdo->lastInsertId();
            }

            // Her ürün için ayrı kayıt oluştur
            foreach ($products as $index => $productData) {
                if (!empty($productData['amount'])) {
                    $urun_id = !empty($productData['product_id']) ? (int)$productData['product_id'] : null;
                    $odeme_tipi = $productData['type'];
                    $miktar = (float)str_replace([',', ' '], ['.', ''], $productData['amount']);
                    $adet = isset($productData['quantity']) ? (int)$productData['quantity'] : 1;
                    $urun_notu = !empty($productData['note']) ? trim($productData['note']) : '';
                    
                    // Eğer ürün ID 0 ise ve yeni ürün adı varsa, manuel ürün olarak işle
                    if ($urun_id === 0 && !empty($productData['new_product_name'])) {
                        $newProductName = trim($productData['new_product_name']);
                        // Manuel ürün için urun_id null kalacak
                        $urun_id = null;
                    }

                    // Ürün kaydını oluştur (ana işleme bağlı)
                    // Önce yeni sütunların var olup olmadığını kontrol et
                    try {
                        $stmt = $pdo->prepare('INSERT INTO islemler (musteri_id, urun_id, odeme_tipi, miktar, aciklama, kullanici_id, parent_transaction_id, is_main_transaction, adet) VALUES (?, ?, ?, ?, ?, ?, ?, 0, ?)');
                        $stmt->execute([$musteri_id, $urun_id, $odeme_tipi, $miktar, $urun_notu, $currentUserId, $mainTransactionId, $adet]);
                        $transactionId = $pdo->lastInsertId();
                        
                        // Eğer manuel ürün adı varsa JSON'a kaydet
                        if ($urun_id === null && !empty($productData['new_product_name'])) {
                            saveManualProduct($transactionId, $productData['new_product_name']);
                        }
                    } catch (Exception $e) {
                        // Eğer yeni sütunlar yoksa, eski formatla ekle
                        if (strpos($e->getMessage(), 'adet') !== false || strpos($e->getMessage(), 'parent_transaction_id') !== false) {
                            $stmt = $pdo->prepare('INSERT INTO islemler (musteri_id, urun_id, odeme_tipi, miktar, aciklama, kullanici_id) VALUES (?, ?, ?, ?, ?, ?)');
                            $stmt->execute([$musteri_id, $urun_id, $odeme_tipi, $miktar, $urun_notu, $currentUserId]);
                            $transactionId = $pdo->lastInsertId();
                            
                            // Eğer manuel ürün adı varsa JSON'a kaydet
                            if ($urun_id === null && !empty($productData['new_product_name'])) {
                                saveManualProduct($transactionId, $productData['new_product_name']);
                            }
                        } else {
                            throw $e;
                        }
                    }

                    if ($odeme_tipi === 'borc') {
                        $totalDebit += $miktar;
                    } else {
                        $totalCredit += $miktar;
                    }
                }
            }

            // Ana işlem kaydını güncelle (toplam tutar ile)
            if ($mainTransactionId) {
                $netAmount = $totalDebit - $totalCredit;
                $stmt = $pdo->prepare('UPDATE islemler SET miktar = ? WHERE id = ?');
                $stmt->execute([$netAmount, $mainTransactionId]);
            }

            // Müşteri bakiyesini güncelle
            $netAmount = $totalDebit - $totalCredit;
            if ($netAmount != 0) {
                $pdo->prepare('UPDATE musteriler SET tutar = tutar + ? WHERE id = ?')->execute([$netAmount, $musteri_id]);
            }

            $pdo->commit();
            // Yönlendirme URL'sini doğru şekilde oluştur
            $redirect_url = 'islemler.php';
            $query_params = [];
            if ($customerId) {
                $query_params['customer'] = $customerId;
            }
            $query_params['success'] = '1';
            $redirect_url .= '?' . http_build_query($query_params);
            header('Location: ' . $redirect_url);
            exit;
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = 'İşlem başarısız: ' . $e->getMessage();
    }
}

require_once __DIR__ . '/includes/header.php';

$customers = $pdo->query('SELECT id, isim FROM musteriler ORDER BY isim ASC')->fetchAll();
$products = $pdo->query('SELECT id, isim FROM urunler ORDER by isim ASC')->fetchAll();
$selectedCustomer = null;
if ($customerId) {
    $st = $pdo->prepare('SELECT * FROM musteriler WHERE id = ?');
    $st->execute([$customerId]);
    $selectedCustomer = $st->fetch();
}

// Filtreleme parametreleri
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$productFilter = isset($_GET['product']) ? (int)$_GET['product'] : 0;
$typeFilter = isset($_GET['type']) ? $_GET['type'] : '';
$dateFrom = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$dateTo = isset($_GET['date_to']) ? $_GET['date_to'] : '';

// Sorgu koşulları oluşturma
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

$whereClause = '';
if (!empty($conditions)) {
    $whereClause = 'WHERE ' . implode(' AND ', $conditions);
}

$countSql = "SELECT COUNT(*) FROM islemler i JOIN musteriler m ON m.id = i.musteri_id ";

// WHERE koşulunu ekle
if (!empty($conditions)) {
    $countSql .= "WHERE (i.is_main_transaction = 1 OR i.is_main_transaction IS NULL) AND " . implode(' AND ', $conditions);
} else {
    $countSql .= "WHERE (i.is_main_transaction = 1 OR i.is_main_transaction IS NULL)";
}
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$totalRows = (int)$countStmt->fetchColumn();
$totalPages = max(1, ceil($totalRows / $perPage));

$sql = "SELECT i.*, m.isim AS musteri_isim, u.isim AS urun_isim, k.isim AS kullanici_isim,
               (SELECT COUNT(*) FROM islemler sub WHERE sub.parent_transaction_id = i.id) as product_count
       FROM islemler i
       JOIN musteriler m ON m.id = i.musteri_id
       LEFT JOIN urunler u ON u.id = i.urun_id
       LEFT JOIN kullanicilar k ON k.id = i.kullanici_id";

// WHERE koşulunu ekle
if (!empty($conditions)) {
    $sql .= " WHERE (i.is_main_transaction = 1 OR i.is_main_transaction IS NULL) AND " . implode(' AND ', $conditions);
} else {
    $sql .= " WHERE (i.is_main_transaction = 1 OR i.is_main_transaction IS NULL)";
}

$sql .= " ORDER BY i.olusturma_zamani DESC LIMIT ? OFFSET ?";

$stmt = $pdo->prepare($sql);
$paramIndex = 1;

foreach ($params as $param) {
    $stmt->bindValue($paramIndex++, $param);
}

$stmt->bindValue($paramIndex++, $perPage, PDO::PARAM_INT);
$stmt->bindValue($paramIndex++, $offset, PDO::PARAM_INT);
$stmt->execute();
$transactions = $stmt->fetchAll();
?>

<div class="container mx-auto px-4 py-6">
    <?php if (isset($_GET['success'])): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm animate-fadeIn" role="alert">
            <div class="flex items-center">
                <div class="py-1"><i class="bi bi-check-circle-fill text-green-500 mr-3"></i></div>
                <div>
                    <p class="font-medium">Başarılı!</p>
                    <p class="text-sm">
                        <?php
                        if ($_GET['success'] == '2') {
                            echo 'İşlem başarıyla güncellendi.';
                        } else if ($_GET['success'] == '3') {
                            echo 'İşlem başarıyla silindi.';
                        } else {
                            echo 'İşlem başarıyla eklendi.';
                        }
                        ?>
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm" role="alert">
            <div class="flex items-center">
                <div class="py-1"><i class="bi bi-exclamation-triangle-fill text-red-500 mr-3"></i></div>
                <div>
                    <p class="font-medium">Hata!</p>
                    <p class="text-sm"><?php echo htmlspecialchars($error); ?></p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center">
                <i class="bi bi-cash-coin mr-2 text-primary-600"></i> İşlem Yönetimi
            </h1>
            <p class="text-sm text-gray-600 mt-1">
                <?php if ($selectedCustomer): ?>
                    <span class="font-medium"><?php echo htmlspecialchars($selectedCustomer['isim']); ?></span> müşterisi için işlemler
                <?php else: ?>
                    Tüm müşteriler için işlem ekle ve geçmişi görüntüle
                <?php endif; ?>
            </p>
        </div>
        <?php if ($selectedCustomer): ?>
            <a href="musteri_rapor.php?customer=<?php echo $customerId; ?>" class="btn btn-secondary flex items-center">
                <i class="bi bi-file-earmark-bar-graph mr-2"></i> Müşteri Raporu
            </a>
        <?php endif; ?>
    </div>

    <!-- İşlem Ekleme Formu -->
    <div class="card-hover animate-fadeIn mb-6 shadow-lg">
        <div class="card-header">
            <h3 class="card-title flex items-center">
                <i class="bi bi-plus-circle mr-2 text-primary-600"></i>
                Yeni İşlem Ekle
            </h3>
        </div>
        <div class="p-5">
            <form method="POST" id="transactionForm" class="grid grid-cols-1 gap-4">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    <div class="md:col-span-4 col-span-1">
                        <label class="form-label flex items-center">
                            <i class="bi bi-person mr-2 text-primary-500"></i> Müşteri
                        </label>
                        <?php if ($selectedCustomer): ?>
                            <input type="hidden" name="customer_id" value="<?php echo $customerId; ?>">
                            <input type="text" class="form-input bg-gray-100" readonly value="<?php echo htmlspecialchars($selectedCustomer['isim']); ?>">
                        <?php else: ?>
                            <div class="relative">
                                <input type="text" id="customer-search" class="form-input" placeholder="Müşteri ara veya yeni müşteri adı yazın..." autocomplete="off" autofocus>
                                <input type="hidden" name="customer_id" id="customer_id" required>
                                <input type="hidden" name="new_customer_name" id="new_customer_name">
                                <input type="hidden" name="new_customer_phone" id="new_customer_phone">
                                <div id="customer-suggestions" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto">
                                    <!-- Öneriler buraya yüklenecek -->
                                </div>
                                <div id="customer-balance" class="mt-2 text-sm text-gray-600 hidden"></div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Çoklu Ürün Alanı -->
                <div id="products-container">
                    <div class="product-row grid grid-cols-1 md:grid-cols-12 gap-4 mb-3">
                        <div class="md:col-span-4 col-span-1">
                            <label class="form-label flex items-center">
                                <i class="bi bi-box-seam mr-2 text-primary-500"></i> Ürün
                            </label>
                            <div class="relative">
                                <input type="text" class="form-input product-search" placeholder="Ürün ara veya yeni ürün adı yazın..." autocomplete="off">
                                <input type="hidden" name="products[0][product_id]" class="product-id" required>
                                <input type="hidden" name="products[0][new_product_name]" class="new-product-name">
                                <div class="product-suggestions absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto">
                                    <!-- Ürün önerileri buraya yüklenecek -->
                                </div>
                            </div>
                        </div>

                        <div class="md:col-span-2 col-span-1">
                            <label class="form-label flex items-center">
                                <i class="bi bi-arrow-left-right mr-2 text-primary-500"></i> İşlem Türü
                            </label>
                            <select name="products[0][type]" class="form-select type-select">
                                <option value="borc">Borç</option>
                                <option value="tahsilat">Tahsilat</option>
                            </select>
                        </div>

                        <div class="md:col-span-2 col-span-1">
                            <label class="form-label flex items-center">
                                <i class="bi bi-123 mr-2 text-primary-500"></i> Adet
                            </label>
                            <input type="number" name="products[0][quantity]" class="form-input quantity-input" value="1" min="1" required>
                        </div>

                        <div class="md:col-span-2 col-span-1">
                            <label class="form-label flex items-center">
                                <i class="bi bi-currency-exchange mr-2 text-primary-500"></i> Birim Fiyat (₺)
                            </label>
                            <input type="text" name="products[0][unit_price]" class="form-input unit-price-input" placeholder="0,00" required>
                        </div>

                        <div class="md:col-span-2 col-span-1">
                            <label class="form-label flex items-center">
                                <i class="bi bi-calculator mr-2 text-primary-500"></i> Toplam (₺)
                            </label>
                            <input type="text" name="products[0][amount]" class="form-input amount-input" placeholder="0,00" readonly>
                        </div>

                        <div class="md:col-span-2 col-span-1">
                            <label class="form-label flex items-center">
                                <i class="bi bi-chat-left-text mr-2 text-primary-500"></i> Not
                            </label>
                            <input type="text" name="products[0][note]" class="form-input" placeholder="">
                        </div>

                        <div class="md:col-span-1 col-span-1 flex items-end">
                            <button type="button" class="btn btn-outline text-red-500 remove-product hidden" title="Kaldır">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex justify-between items-center mt-4">
                    <button type="button" id="add-product" class="btn btn-outline btn-sm">
                        <i class="bi bi-plus-circle mr-1"></i> Ürün Ekle
                    </button>

                    <button type="submit" class="btn btn-primary flex items-center shadow-sm hover:shadow-md transition-all" id="submitButton">
                        <i class="bi bi-plus-circle mr-2"></i> İşlemleri Ekle
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Arama ve Filtreleme -->
    <div class="card-hover animate-fadeIn mb-6 shadow-lg">
        <div class="card-header">
            <h3 class="card-title flex items-center">
                <i class="bi bi-search mr-2 text-primary-600"></i>
                Arama ve Filtreleme
            </h3>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <?php if ($customerId): ?>
                    <input type="hidden" id="customer-filter" value="<?php echo $customerId; ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="search" class="form-label">Arama</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="bi bi-search text-gray-400"></i>
                        </span>
                        <input type="text" id="search" class="form-input pl-10" placeholder="Müşteri adı veya not..." value="<?php echo htmlspecialchars($search); ?>">
                        <div id="search-loading" class="absolute inset-y-0 right-0 flex items-center pr-3 hidden">
                            <div class="spinner-border spinner-border-sm text-primary-500" role="status">
                                <span class="sr-only">Yükleniyor...</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="product" class="form-label">Ürün</label>
                    <select id="product" class="form-select">
                        <option value="">Tüm Ürünler</option>
                        <?php foreach ($products as $product): ?>
                            <option value="<?php echo $product['id']; ?>" <?php echo $productFilter == $product['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($product['isim']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="type" class="form-label">İşlem Türü</label>
                    <select id="type" class="form-select">
                        <option value="">Tümü</option>
                        <option value="borc" <?php echo $typeFilter === 'borc' ? 'selected' : ''; ?>>Borç</option>
                        <option value="tahsilat" <?php echo $typeFilter === 'tahsilat' ? 'selected' : ''; ?>>Tahsilat</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div class="form-group">
                        <label for="date_from" class="form-label">Başlangıç Tarihi</label>
                        <input type="date" id="date_from" class="form-input" value="<?php echo $dateFrom; ?>">
                    </div>
                    <div class="form-group">
                        <label for="date_to" class="form-label">Bitiş Tarihi</label>
                        <input type="date" id="date_to" class="form-input" value="<?php echo $dateTo; ?>">
                    </div>
                </div>

                <div class="col-span-full flex items-center justify-end gap-2 mt-2">
                    <div id="filter-loading" class="hidden">
                        <div class="flex items-center text-primary-500">
                            <div class="spinner-border spinner-border-sm mr-2" role="status">
                                <span class="sr-only">Yükleniyor...</span>
                            </div>
                            <span class="text-sm">Filtreleniyor...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- İşlem Geçmişi -->
    <div class="card-hover animate-fadeIn shadow-lg" style="animation-delay: 0.2s">
        <div class="card-header">
            <div class="flex justify-between items-center">
                <h3 class="card-title flex items-center">
                    <i class="bi bi-clock-history mr-2 text-primary-600"></i>
                    <span id="table-title">
                        <?php if ($customerId && $selectedCustomer): ?>
                            <?php echo htmlspecialchars($selectedCustomer['isim']); ?> - İşlem Geçmişi
                        <?php else: ?>
                            Son İşlemler
                        <?php endif; ?>
                    </span>
                </h3>
                <?php if ($customerId && $selectedCustomer): ?>
                    <a href="musteri_rapor.php?customer=<?php echo $customerId; ?>" class="btn btn-secondary btn-sm flex items-center">
                        <i class="bi bi-printer mr-2"></i> Yazdır
                    </a>
                <?php endif; ?>
            </div>
        </div>
        <div class="p-0">
            <div class="table-container">
                <div id="transactions-table-container">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <?php if (!$customerId): ?>
                                    <th><i class="bi bi-person-badge mr-1 text-primary-500"></i> Müşteri</th>
                                <?php endif; ?>
                                <th><i class="bi bi-box-seam mr-1 text-primary-500"></i> Ürün</th>
                                <th><i class="bi bi-calendar-date mr-1 text-primary-500"></i> Tarih</th>
                                <th><i class="bi bi-currency-exchange mr-1 text-primary-500"></i> Tutar (₺)</th>
                                <th><i class="bi bi-arrow-left-right mr-1 text-primary-500"></i> Tür</th>
                                <th><i class="bi bi-chat-left-text mr-1 text-primary-500"></i> Açıklama</th>
                                <th><i class="bi bi-person-plus mr-1 text-primary-500"></i> Ekleyen</th>
                                <th class="text-right"><i class="bi bi-gear-fill text-primary-500"></i> İşlemler</th>
                            </tr>
                        </thead>
                        <tbody id="transactions-tbody">
                            <?php
                            $hasTransactions = false;
                            $index = 0;
                            foreach ($transactions as $row):
                                $hasTransactions = true;
                                $index++;
                                ?>
                                <tr class="animate-fadeIn" style="animation-delay: <?php echo 0.3 + ($index * 0.05); ?>s">
                                    <?php if (!$customerId): ?>
                                        <td>
                                            <a href="islemler.php?customer=<?php echo $row['musteri_id']; ?>" class="text-primary-600 hover:text-primary-900 font-medium">
                                                <?php echo htmlspecialchars($row['musteri_isim']); ?>
                                            </a>
                                        </td>
                                    <?php endif; ?>
                                    <td>
                                        <?php if (isset($row['product_count']) && $row['product_count'] > 0): ?>
                                            <?php
                                            // Çoklu ürün için alt ürünleri al ve manuel ürün adlarını JSON'dan çek
                                            $subProductStmt = $pdo->prepare('SELECT i.*, u.isim AS urun_isim FROM islemler i LEFT JOIN urunler u ON u.id = i.urun_id WHERE i.parent_transaction_id = ? ORDER BY i.id ASC');
                                            $subProductStmt->execute([$row['id']]);
                                            $subProducts = $subProductStmt->fetchAll();
                                            
                                            $productNames = [];
                                            foreach ($subProducts as $subProduct) {
                                                if ($subProduct['urun_isim']) {
                                                    $productNames[] = $subProduct['urun_isim'];
                                                } else {
                                                    $manualName = getManualProduct($subProduct['id']);
                                                    $productNames[] = $manualName ?: 'Manuel Ürün';
                                                }
                                            }
                                            ?>
                                            <span class="badge badge-outline"><?php echo htmlspecialchars(implode(' - ', $productNames)); ?></span>
                                        <?php elseif (isset($row['urun_isim']) && $row['urun_isim']): ?>
                                            <span class="badge badge-outline"><?php echo htmlspecialchars($row['urun_isim']); ?></span>
                                        <?php else: ?>
                                            <?php 
                                            // Tek ürün için manuel ürün adını JSON'dan al
                                            $manualName = getManualProduct($row['id']);
                                            if ($manualName): ?>
                                                <span class="badge badge-outline"><?php echo htmlspecialchars($manualName); ?></span>
                                            <?php else: ?>
                                                <span class="text-gray-400">-</span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('d.m.Y H:i', strtotime($row['olusturma_zamani'])); ?></td>
                                    <td class="font-medium"><?php echo number_format($row['miktar'], 2, ',', '.'); ?> ₺</td>
                                    <td>
                                        <?php if ($row['odeme_tipi'] === 'borc'): ?>
                                            <span class="badge-debit flex items-center w-fit"><i class="bi bi-arrow-down-right mr-1"></i> Borç</span>
                                        <?php else: ?>
                                            <span class="badge-credit flex items-center w-fit"><i class="bi bi-arrow-up-right mr-1"></i> Tahsilat</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($row['aciklama']): ?>
                                            <?php echo htmlspecialchars($row['aciklama']); ?>
                                        <?php else: ?>
                                            <span class="text-gray-400 italic">Not girilmedi</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="text-sm text-gray-600 bg-gray-100 px-2 py-1 rounded-full" title="İşlemi ekleyen kullanıcı">
                                            <i class="bi bi-person-fill mr-1 text-primary-500"></i>
                                            <?php echo isset($row['kullanici_isim']) && !empty($row['kullanici_isim']) ? htmlspecialchars($row['kullanici_isim']) : 'Sistem'; ?>
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        <div class="flex justify-end gap-2">
                                            <a href="yazdir.php?id=<?php echo $row['id']; ?>" class="btn btn-outline btn-sm" title="Yazdır" target="_blank">
                                                <i class="bi bi-printer"></i>
                                            </a>
                                            <a href="islemler.php?edit=<?php echo $row['id']; ?><?php echo $customerId ? '&customer=' . $customerId : ''; ?><?php echo isset($_GET['page']) ? '&page=' . $_GET['page'] : ''; ?>" class="btn btn-outline btn-sm text-primary" title="Düzenle">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <a href="islemler.php?delete=<?php echo $row['id']; ?><?php echo $customerId ? '&customer=' . $customerId : ''; ?><?php echo isset($_GET['page']) ? '&page=' . $_GET['page'] : ''; ?>" class="btn btn-outline btn-sm text-danger" title="Sil" onclick="return confirm('Bu işlemi silmek istediğinize emin misiniz?')">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>

                            <?php if (!$hasTransactions): ?>
                                <tr>
                                    <td colspan="<?php echo $customerId ? '7' : '8'; ?>" class="text-center py-12 text-gray-500">
                                        <div class="flex flex-col items-center justify-center gap-3">
                                            <div class="bg-gray-100 rounded-full p-4 mb-2">
                                                <i class="bi bi-receipt text-5xl text-primary-500"></i>
                                            </div>
                                            <h4 class="text-lg font-medium">Henüz işlem bulunmuyor</h4>
                                            <p class="text-sm text-gray-400 max-w-md">
                                                <?php if ($customerId): ?>
                                                    Bu müşteri için henüz bir işlem kaydı oluşturulmamış. Yukarıdaki formu kullanarak yeni bir işlem ekleyebilirsiniz.
                                                <?php else: ?>
                                                    Sistemde henüz bir işlem kaydı bulunmuyor. Yukarıdaki formu kullanarak yeni bir işlem ekleyebilirsiniz.
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Sayfalama -->
    <div id="pagination-container" class="flex justify-center mt-6">
        <?php if ($totalPages > 1): ?>
            <nav class="inline-flex rounded-md shadow-sm" aria-label="Sayfalama">
                <?php if ($page > 1): ?>
                    <a href="islemler.php?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>"
                       class="px-3 py-2 border border-gray-300 rounded-l-md bg-white text-gray-700 hover:bg-gray-100">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i == 1 || $i == $totalPages || ($i >= $page - 1 && $i <= $page + 1)): ?>
                        <a href="islemler.php?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>"
                           class="px-3 py-2 border border-gray-300 <?php echo $i == $page ? 'bg-primary-600 text-white border-primary-600' : 'bg-white text-gray-700 hover:bg-gray-100'; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php elseif (($i == 2 && $page > 3) || ($i == $totalPages - 1 && $page < $totalPages - 2)): ?>
                        <span class="px-3 py-2 border border-gray-300 bg-white text-gray-700">...</span>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="islemler.php?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>"
                       class="px-3 py-2 border border-gray-300 rounded-r-md bg-white text-gray-700 hover:bg-gray-100">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </nav>
        <?php endif; ?>
    </div>
</div>

<!-- İşlem Düzenleme Modal -->
<?php if ($editTransaction): ?>
    <div id="editTransactionModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 animate-fadeIn">
            <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="bi bi-pencil-square mr-2 text-primary-600"></i> İşlem Düzenle
                </h3>
                <button type="button" class="text-gray-400 hover:text-gray-500 close-modal">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <form method="POST" action="">
                <div class="p-4">
                    <input type="hidden" name="transaction_id" value="<?php echo (int)$editTransaction['id']; ?>">
                    <input type="hidden" name="action" value="update_transaction">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="form-label flex items-center">
                                <i class="bi bi-person mr-2 text-primary-500"></i> Müşteri
                            </label>
                            <input type="text" class="form-input bg-gray-100" readonly value="<?php echo htmlspecialchars($editTransaction['musteri_isim']); ?>">
                        </div>

                        <div>
                            <label class="form-label flex items-center">
                                <i class="bi bi-box-seam mr-2 text-primary-500"></i> Ürün
                            </label>
                            <select name="product_id" class="form-select">
                                <option value="">Ürün Seçiniz (Opsiyonel)</option>
                                <?php foreach ($products as $product): ?>
                                    <option value="<?php echo $product['id']; ?>" <?php echo $editTransaction && $editTransaction['urun_id'] == $product['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($product['isim']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="form-label flex items-center">
                                <i class="bi bi-arrow-left-right mr-2 text-primary-500"></i> İşlem Türü
                            </label>
                            <select name="type" class="form-select">
                                <option value="borc" <?php echo $editTransaction && $editTransaction['odeme_tipi'] === 'borc' ? 'selected' : ''; ?>>Borç</option>
                                <option value="tahsilat" <?php echo $editTransaction && $editTransaction['odeme_tipi'] === 'tahsilat' ? 'selected' : ''; ?>>Tahsilat</option>
                            </select>
                        </div>

                        <div>
                            <label class="form-label flex items-center">
                                <i class="bi bi-person-fill mr-2 text-primary-500"></i> İşlemi Ekleyen
                        </label>
                        <input type="text" class="form-input bg-gray-100" readonly value="<?php echo isset($editTransaction['kullanici_isim']) && !empty($editTransaction['kullanici_isim']) ? htmlspecialchars($editTransaction['kullanici_isim']) : 'Sistem'; ?>">
                    </div>

                        <div>
                            <label class="form-label flex items-center">
                                <i class="bi bi-currency-exchange mr-2 text-primary-500"></i> Tutar (₺)
                            </label>
                            <input type="text" name="amount" class="form-input" placeholder="0,00" required value="<?php echo $editTransaction ? number_format($editTransaction['miktar'], 2, ',', '.') : ''; ?>">
                        </div>

                        <div class="md:col-span-2">
                            <label class="form-label flex items-center">
                                <i class="bi bi-chat-left-text mr-2 text-primary-500"></i> Açıklama
                            </label>
                            <input type="text" name="note" class="form-input" placeholder="İşlem açıklaması" value="<?php echo $editTransaction ? htmlspecialchars($editTransaction['aciklama']) : ''; ?>">
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 border-t border-gray-200 flex justify-end gap-2">
                    <button type="button" class="btn btn-outline close-modal">
                        <i class="bi bi-x-circle mr-1"></i> İptal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle mr-1"></i> Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>
    </div>
<?php endif; ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Çoklu ürün yönetimi
        let productCounter = 1;
        const productsContainer = document.getElementById('products-container');
        const addProductButton = document.getElementById('add-product');

        if (addProductButton && productsContainer) {
            addProductButton.addEventListener('click', function () {
                const newRow = document.createElement('div');
                newRow.className = 'product-row grid grid-cols-1 md:grid-cols-12 gap-4 mb-3';
                newRow.innerHTML = `
                <div class="md:col-span-4 col-span-1">
                    <label class="form-label flex items-center">
                        <i class="bi bi-box-seam mr-2 text-primary-500"></i> Ürün
                    </label>
                    <div class="relative">
                        <input type="text" class="form-input product-search" placeholder="Ürün ara veya yeni ürün adı yazın..." autocomplete="off">
                        <input type="hidden" name="products[${productCounter}][product_id]" class="product-id" required>
                        <input type="hidden" name="products[${productCounter}][new_product_name]" class="new-product-name">
                        <div class="product-suggestions absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto"></div>
                    </div>
                </div>
                
                <div class="md:col-span-2 col-span-1">
                    <label class="form-label flex items-center">
                        <i class="bi bi-arrow-left-right mr-2 text-primary-500"></i> İşlem Türü
                    </label>
                    <select name="products[${productCounter}][type]" class="form-select type-select">
                        <option value="borc">Borç</option>
                        <option value="tahsilat">Tahsilat</option>
                    </select>
                </div>
                
                <div class="md:col-span-2 col-span-1">
                    <label class="form-label flex items-center">
                        <i class="bi bi-123 mr-2 text-primary-500"></i> Adet
                    </label>
                    <input type="number" name="products[${productCounter}][quantity]" class="form-input quantity-input" value="1" min="1" required>
                </div>

                <div class="md:col-span-2 col-span-1">
                    <label class="form-label flex items-center">
                        <i class="bi bi-currency-exchange mr-2 text-primary-500"></i> Birim Fiyat (₺)
                    </label>
                    <input type="text" name="products[${productCounter}][unit_price]" class="form-input unit-price-input" placeholder="0,00" required>
                </div>

                <div class="md:col-span-2 col-span-1">
                    <label class="form-label flex items-center">
                        <i class="bi bi-calculator mr-2 text-primary-500"></i> Toplam (₺)
                    </label>
                    <input type="text" name="products[${productCounter}][amount]" class="form-input amount-input" placeholder="0,00" readonly>
                </div>

                <div class="md:col-span-1 col-span-1">
                    <label class="form-label flex items-center">
                        <i class="bi bi-chat-left-text mr-2 text-primary-500"></i> Not
                    </label>
                    <input type="text" name="products[${productCounter}][note]" class="form-input" placeholder="Not">
                </div>
                
                <div class="md:col-span-1 col-span-1 flex items-end">
                    <button type="button" class="btn btn-outline text-red-500 remove-product" title="Kaldır">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            `;

                productsContainer.appendChild(newRow);
                // Yeni eklenen ürün arama alanını etkinleştir
                const newlyAddedSearch = newRow.querySelector('.product-search');
                if (newlyAddedSearch) {
                    setupProductSearch(newlyAddedSearch);
                }
                productCounter++;

                // Yeni eklenen satırdaki input alanlarını ayarla
                setupAmountInputs();
                // Tüm remove butonlarını güncelle
                updateRemoveButtons();
            });
        }

        function updateRemoveButtons() {
            const removeButtons = document.querySelectorAll('.remove-product');
            const productRows = document.querySelectorAll('.product-row');

            removeButtons.forEach((button, index) => {
                // İlk satırdaki remove butonunu gizle, diğerlerini göster
                if (index === 0 && productRows.length === 1) {
                    button.classList.add('hidden');
                } else {
                    button.classList.remove('hidden');
                }

                button.onclick = function () {
                    if (productRows.length > 1) {
                        button.closest('.product-row').remove();
                        updateRemoveButtons();
                    }
                };
            });
        }

        // Tutar alanları için para formatı ve hesaplama
        function setupAmountInputs() {
            const unitPriceInputs = document.querySelectorAll('.unit-price-input');
            const quantityInputs = document.querySelectorAll('.quantity-input');
            const amountInputs = document.querySelectorAll('.amount-input');

            // Birim fiyat formatı
            unitPriceInputs.forEach(input => {
                // Önce mevcut event listener'ları temizle
                input.removeEventListener('input', input._inputHandler);
                
                // Yeni event handler oluştur
                input._inputHandler = function (e) {
                    let value = e.target.value.replace(/[^\d,]/g, '');
                    value = value.replace(',', '.');
                    if (value.includes('.')) {
                        const parts = value.split('.');
                        if (parts[1] && parts[1].length > 2) {
                            parts[1] = parts[1].substring(0, 2);
                        }
                        value = parts.join('.');
                    }
                    e.target.value = value;
                    calculateTotal(e.target);
                };
                
                input.addEventListener('input', input._inputHandler);
            });

            // Adet değişikliği
            quantityInputs.forEach(input => {
                // Önce mevcut event listener'ları temizle
                input.removeEventListener('input', input._quantityHandler);
                
                // Yeni event handler oluştur
                input._quantityHandler = function (e) {
                    calculateTotal(e.target);
                };
                
                input.addEventListener('input', input._quantityHandler);
            });

            // Toplam hesaplama fonksiyonu
            function calculateTotal(input) {
                const row = input.closest('.product-row');
                if (!row) return;

                const unitPriceInput = row.querySelector('.unit-price-input');
                const quantityInput = row.querySelector('.quantity-input');
                const amountInput = row.querySelector('.amount-input');

                if (!unitPriceInput || !quantityInput || !amountInput) return;

                const unitPrice = parseFloat(unitPriceInput.value.replace(',', '.')) || 0;
                const quantity = parseInt(quantityInput.value) || 1;
                const total = unitPrice * quantity;

                amountInput.value = total.toFixed(2).replace('.', ',');
            }
        }

        // Form gönderildiğinde buton durumunu değiştir
        const form = document.getElementById('transactionForm');
        if (form) {
            form.addEventListener('submit', function () {
                const submitButton = document.getElementById('submitButton');
                if (submitButton) {
                    submitButton.innerHTML = '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span> İşleniyor...';
                    submitButton.disabled = true;
                }
            });
        }

        // Modal işlemleri
        const modal = document.getElementById('editTransactionModal');
        if (modal) {
            const closeButtons = document.querySelectorAll('.close-modal');

            const closeModal = () => {
                modal.classList.add('hidden');
                // URL'yi temizle
                const url = new URL(window.location);
                url.searchParams.delete('edit');
                window.history.replaceState({}, document.title, url);
            };

            closeButtons.forEach(button => {
                button.addEventListener('click', closeModal);
            });

            // Modal dışına tıklandığında kapat
            modal.addEventListener('click', function (e) {
                if (e.target === modal) {
                    closeModal();
                }
            });

            // Escape tuşu ile kapat
            document.addEventListener('keydown', function (e) {
                if (e.key === "Escape" && !modal.classList.contains('hidden')) {
                    closeModal();
                }
            });
        }

        // İlk yüklemede remove butonlarını güncelle ve amount inputlarını ayarla
        updateRemoveButtons();
        setupAmountInputs();
        
        // İlk satır için de event listener'ları ayarla
        document.querySelectorAll('.product-search').forEach(setupProductSearch);
        
        // İlk satır için hesaplama fonksiyonunu test et
        setTimeout(() => {
            const firstQuantityInput = document.querySelector('.quantity-input');
            const firstUnitPriceInput = document.querySelector('.unit-price-input');
            if (firstQuantityInput && firstUnitPriceInput) {
                // İlk satır için hesaplama yap
                const row = firstQuantityInput.closest('.product-row');
                if (row) {
                    const unitPriceInput = row.querySelector('.unit-price-input');
                    const quantityInput = row.querySelector('.quantity-input');
                    const amountInput = row.querySelector('.amount-input');
                    
                    if (unitPriceInput && quantityInput && amountInput) {
                        const unitPrice = parseFloat(unitPriceInput.value.replace(',', '.')) || 0;
                        const quantity = parseInt(quantityInput.value) || 1;
                        const total = unitPrice * quantity;
                        amountInput.value = total.toFixed(2).replace('.', ',');
                    }
                }
            }
        }, 100);

        // Fiyat güncelleme fonksiyonu
        function updatePrice(quantityInput, amountInput) {
            if (!amountInput || !quantityInput) return;
            
            const basePrice = parseFloat(amountInput.getAttribute('data-base-price') || amountInput.value.replace(',', '.'));
            if (!isNaN(basePrice)) {
                const quantity = parseInt(quantityInput.value) || 1;
                const newPrice = (basePrice * quantity).toFixed(2);
                amountInput.value = newPrice.replace('.', ',');
            }
        }
        
        // Müşteri arama özelliği
        const customerSearch = document.getElementById('customer-search');
        const customerSuggestions = document.getElementById('customer-suggestions');
        const customerIdInput = document.getElementById('customer_id');
        
        if (customerSearch) {
            // Sayfa yüklendiğinde arama alanını temizle
            customerSearch.value = '';
            customerIdInput.value = '';
            document.getElementById('new_customer_name').value = '';
            document.getElementById('new_customer_phone').value = '';
            let customerTimeout;
            
            customerSearch.addEventListener('input', function() {
                clearTimeout(customerTimeout);
                const query = this.value.trim();
                
                if (query.length < 2) {
                    customerSuggestions.classList.add('hidden');
                    customerIdInput.value = '';
                    return;
                }
                
                customerTimeout = setTimeout(() => {
                    fetch(`musteriara.php?q=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.length > 0) {
                                customerSuggestions.innerHTML = data.map(customer => 
                                    `<div class="p-3 hover:bg-gray-100 cursor-pointer border-b border-gray-200 last:border-b-0" data-id="${customer.id}" data-name="${customer.isim}">
                                        <div class="font-medium">${customer.isim}</div>
                                        <div class="text-sm text-gray-500">${customer.numara || 'Telefon yok'}</div>
                                    </div>`
                                ).join('');
                                customerSuggestions.classList.remove('hidden');
                            } else {
                                customerSuggestions.innerHTML = `<div class="p-3 text-gray-500">"${query}" için müşteri bulunamadı. Yeni müşteri olarak eklenebilir.</div>`;
                                customerSuggestions.classList.remove('hidden');
                            }
                        })
                        .catch(error => {
                            console.error('Müşteri arama hatası:', error);
                        });
                }, 300);
            });
            
            // Müşteri seçimi
            customerSuggestions.addEventListener('click', function(e) {
                const item = e.target.closest('[data-id]');
                if (item) {
                    const id = item.dataset.id;
                    const name = item.dataset.name;
                    
                    customerSearch.value = name;
                    customerIdInput.value = id;
                    document.getElementById('new_customer_name').value = '';
                    document.getElementById('new_customer_phone').value = '';
                    customerSuggestions.classList.add('hidden');
                    // Bakiye getir
                    try { fetchCustomerBalance(id); } catch(e){}
                }
            });
            
            // Enter tuşu ile yeni müşteri ekleme veya arama yapma
            customerSearch.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const query = this.value.trim();
                    if (query.length > 0) {
                        // Eğer öneriler gösteriliyorsa ve bir öneri seçiliyse, onu seç
                        const selectedSuggestion = customerSuggestions.querySelector('[data-id]');
                        if (selectedSuggestion) {
                            const id = selectedSuggestion.dataset.id;
                            const name = selectedSuggestion.dataset.name;
                            customerSearch.value = name;
                            customerIdInput.value = id;
                            document.getElementById('new_customer_name').value = '';
                            document.getElementById('new_customer_phone').value = '';
                            customerSuggestions.classList.add('hidden');
                            try { fetchCustomerBalance(id); } catch(e){}
                        } else {
                            // Eğer öneri yoksa, yeni müşteri olarak ekle
                            customerIdInput.value = '0';
                            document.getElementById('new_customer_name').value = query;
                            customerSuggestions.classList.add('hidden');
                            // Yeni müşterilerde başlangıç bakiye 0 kabul edilir
                            try { showBalanceInfo({ bakiye: 0 }); } catch(e){}
                        }
                    }
                }
            });
            
            // Dışarı tıklandığında önerileri gizle
            document.addEventListener('click', function(e) {
                if (!customerSearch.contains(e.target) && !customerSuggestions.contains(e.target)) {
                    customerSuggestions.classList.add('hidden');
                }
            });
        }
        
        // Ürün arama özelliği
        function setupProductSearch(productSearchInput) {
            const productSuggestions = productSearchInput.parentElement.querySelector('.product-suggestions');
            const productIdInput = productSearchInput.parentElement.querySelector('.product-id');
            
            let productTimeout;
            
            productSearchInput.addEventListener('input', function() {
                clearTimeout(productTimeout);
                const query = this.value.trim();
                
                if (query.length < 2) {
                    productSuggestions.classList.add('hidden');
                    productIdInput.value = '';
                    return;
                }
                
                productTimeout = setTimeout(() => {
                    fetch(`urunara.php?q=${encodeURIComponent(query)}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.length > 0) {
                                productSuggestions.innerHTML = data.map(product => 
                                    `<div class="p-3 hover:bg-gray-100 cursor-pointer border-b border-gray-200 last:border-b-0" data-id="${product.id}" data-name="${product.isim}">
                                        <div class="font-medium">${product.isim}</div>
                                        <div class="text-sm text-gray-500">${product.fiyat ? product.fiyat + ' ₺' : 'Fiyat belirtilmemiş'}</div>
                                    </div>`
                                ).join('');
                                productSuggestions.classList.remove('hidden');
                            } else {
                                productSuggestions.innerHTML = `<div class="p-3 text-gray-500">"${query}" için ürün bulunamadı. Yeni ürün olarak eklenebilir.</div>`;
                                productSuggestions.classList.remove('hidden');
                            }
                        })
                        .catch(error => {
                            console.error('Ürün arama hatası:', error);
                        });
                }, 300);
            });
            
            // Ürün seçimi
            productSuggestions.addEventListener('click', function(e) {
                const item = e.target.closest('[data-id]');
                if (item) {
                    const id = item.dataset.id;
                    const name = item.dataset.name;
                    
                    productSearchInput.value = name;
                    productIdInput.value = id;
                    productSearchInput.parentElement.querySelector('.new-product-name').value = '';
                    productSuggestions.classList.add('hidden');
                    // Fiyatı birim fiyat alanına otomatik doldur
                    const row = productSearchInput.closest('.product-row');
                    const unitPriceInput = row ? row.querySelector('.unit-price-input') : null;
                    const quantityInput = row ? row.querySelector('.quantity-input') : null;
                    const amountInput = row ? row.querySelector('.amount-input') : null;
                    
                    // Ürün fiyatını birim fiyat alanına doldur
                    fetch(`urunara.php?q=${encodeURIComponent(name)}`)
                        .then(r => r.json())
                        .then(list => {
                            const found = Array.isArray(list) ? list.find(p => String(p.id) === String(id)) : null;
                            if (found && unitPriceInput) {
                                const val = Number(found.fiyat || 0);
                                if (!isNaN(val) && val > 0) {
                                    unitPriceInput.value = val.toFixed(2).replace('.', ',');
                                    // Toplam hesapla
                                    if (quantityInput && amountInput) {
                                        const quantity = parseInt(quantityInput.value) || 1;
                                        const total = val * quantity;
                                        amountInput.value = total.toFixed(2).replace('.', ',');
                                    }
                                }
                            }
                        })
                        .catch(() => {});
                }
            });
            
            // Enter tuşu ile yeni ürün ekleme
            productSearchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const query = this.value.trim();
                    if (query.length > 0) {
                        productIdInput.value = '0';
                        productSearchInput.parentElement.querySelector('.new-product-name').value = query;
                        productSuggestions.classList.add('hidden');
                    }
                }
            });
            
            // Dışarı tıklandığında önerileri gizle ve manuel ürün kontrolü yap
            document.addEventListener('click', function(e) {
                if (!productSearchInput.contains(e.target) && !productSuggestions.contains(e.target)) {
                    productSuggestions.classList.add('hidden');
                    
                    // Eğer arama alanında değer var ama ürün seçilmemişse, manuel ürün olarak işle
                    const query = productSearchInput.value.trim();
                    if (query.length > 0 && (!productIdInput.value || productIdInput.value === '')) {
                        productIdInput.value = '0';
                        productSearchInput.parentElement.querySelector('.new-product-name').value = query;
                    }
                }
            });
            
            // Form submit edilmeden önce manuel ürün kontrolü
            productSearchInput.addEventListener('blur', function() {
                const query = this.value.trim();
                if (query.length > 0 && (!productIdInput.value || productIdInput.value === '')) {
                    productIdInput.value = '0';
                    productSearchInput.parentElement.querySelector('.new-product-name').value = query;
                }
            });
        }
        
        // Mevcut ürün arama alanlarını ayarla
        document.querySelectorAll('.product-search').forEach(setupProductSearch);
        
        // Yeni ürün satırı eklendiğinde arama özelliğini ayarla
        const originalAddProduct = window.addProduct;
        window.addProduct = function() {
            originalAddProduct();
            // Yeni eklenen ürün arama alanını ayarla
            const newProductSearch = document.querySelectorAll('.product-search');
            setupProductSearch(newProductSearch[newProductSearch.length - 1]);
        };

        // Bakiye yardımcıları
        function fetchCustomerBalance(id) {
            const info = document.getElementById('customer-balance');
            if (!info) return;
            info.classList.remove('hidden');
            info.innerHTML = '<span class="text-gray-500">Bakiye yükleniyor...</span>';
            fetch(`musteri_ozeti.php?customer=${encodeURIComponent(id)}`)
                .then(r => r.json())
                .then(data => {
                    if (!data.success) throw new Error();
                    showBalanceInfo(data.data);
                })
                .catch(() => {
                    info.innerHTML = '<span class="text-red-600">Bakiye getirilemedi</span>';
                });
        }

        function showBalanceInfo(data) {
            const info = document.getElementById('customer-balance');
            if (!info) return;
            const bakiye = Number(data.bakiye || 0);
            const cls = bakiye > 0 ? 'text-red-600' : (bakiye < 0 ? 'text-green-600' : 'text-gray-700');
            info.innerHTML = `Mevcut bakiye: <span class="font-medium ${cls}">${bakiye.toLocaleString('tr-TR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })} ₺</span>`;
        }
    });

    // AJAX tabanlı arama ve filtreleme sistemi
    document.addEventListener('DOMContentLoaded', function() {
        let searchTimeout;
        let currentPage = 1;
        let isLoading = false;
        
        // Filtre elementlerini seç
        const searchInput = document.getElementById('search');
        const productSelect = document.getElementById('product');
        const typeSelect = document.getElementById('type');
        const dateFromInput = document.getElementById('date_from');
        const dateToInput = document.getElementById('date_to');
        const clearFiltersBtn = document.getElementById('clear-filters');
        const searchLoading = document.getElementById('search-loading');
        const filterLoading = document.getElementById('filter-loading');
        
        // Müşteri ID'sini al
        const customerId = document.getElementById('customer-filter') ? 
            document.getElementById('customer-filter').value : null;
        
        // Arama fonksiyonu
        function performSearch(page = 1) {
            if (isLoading) return;
            
            isLoading = true;
            currentPage = page;
            
            // Loading göstergelerini göster
            if (searchInput.value.trim()) {
                searchLoading.classList.remove('hidden');
            }
            filterLoading.classList.remove('hidden');
            
            // Filtre parametrelerini hazırla
            const params = new URLSearchParams();
            if (customerId) params.append('customer', customerId);
            if (page > 1) params.append('page', page);
            if (searchInput.value.trim()) params.append('search', searchInput.value.trim());
            if (productSelect.value) params.append('product', productSelect.value);
            if (typeSelect.value) params.append('type', typeSelect.value);
            if (dateFromInput.value) params.append('date_from', dateFromInput.value);
            if (dateToInput.value) params.append('date_to', dateToInput.value);
            
            // AJAX isteği gönder
            fetch(`api_search.php?${params.toString()}`)
                .then(async response => {
                    const contentType = response.headers.get('content-type') || '';
                    if (!contentType.includes('application/json')) {
                        throw new Error('Geçersiz yanıt formatı');
                    }
                    const data = await response.json();
                    if (!response.ok) {
                        const msg = data && data.message ? data.message : 'Arama sırasında bir hata oluştu.';
                        throw new Error(msg);
                    }
                    if (!data.success) {
                        const msg = data && data.message ? data.message : 'Arama sırasında bir hata oluştu.';
                        throw new Error(msg);
                    }
                    updateTable(data.data);
                    updatePagination(data.data.pagination);
                    updateTableTitle(data.data.selected_customer);
                })
                .catch(error => {
                    console.error('Arama hatası:', error);
                    showError(error.message || 'Arama sırasında bir hata oluştu.');
                })
                .finally(() => {
                    isLoading = false;
                    searchLoading.classList.add('hidden');
                    filterLoading.classList.add('hidden');
                });
        }
        
        // Tabloyu güncelle
        function updateTable(data) {
            const tbody = document.getElementById('transactions-tbody');
            const customerId = document.getElementById('customer-filter') ? 
                document.getElementById('customer-filter').value : null;
            
            if (data.transactions.length === 0) {
                const colspan = customerId ? '7' : '8';
                tbody.innerHTML = `
                    <tr>
                        <td colspan="${colspan}" class="text-center py-12 text-gray-500">
                            <div class="flex flex-col items-center justify-center gap-3">
                                <div class="bg-gray-100 rounded-full p-4 mb-2">
                                    <i class="bi bi-receipt text-5xl text-primary-500"></i>
                                </div>
                                <h4 class="text-lg font-medium">Henüz işlem bulunmuyor</h4>
                                <p class="text-sm text-gray-400 max-w-md">
                                    ${customerId ? 
                                        'Bu müşteri için henüz bir işlem kaydı oluşturulmamış. Yukarıdaki formu kullanarak yeni bir işlem ekleyebilirsiniz.' :
                                        'Sistemde henüz bir işlem kaydı bulunmuyor. Yukarıdaki formu kullanarak yeni bir işlem ekleyebilirsiniz.'
                                    }
                                </p>
                            </div>
                        </td>
                    </tr>
                `;
                return;
            }
            
            let html = '';
            data.transactions.forEach((row, index) => {
                const date = new Date(row.olusturma_zamani);
                const formattedDate = date.toLocaleDateString('tr-TR') + ' ' + 
                    date.toLocaleTimeString('tr-TR', {hour: '2-digit', minute: '2-digit'});
                
                const amount = parseFloat(row.miktar).toLocaleString('tr-TR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
                
                const isDebit = row.odeme_tipi === 'borc';
                const typeBadge = isDebit ? 
                    '<span class="badge-debit flex items-center w-fit"><i class="bi bi-arrow-down-right mr-1"></i> Borç</span>' :
                    '<span class="badge-credit flex items-center w-fit"><i class="bi bi-arrow-up-right mr-1"></i> Tahsilat</span>';
                
                const productName = row.product_count && row.product_count > 0 ? 
                    `<span class="badge badge-primary">${row.product_count} kalem ürün</span>` :
                    (row.urun_isim ? 
                        `<span class="badge badge-outline">${escapeHtml(row.urun_isim)}</span>` :
                        (row.new_product_name ? 
                            `<span class="badge badge-outline">${escapeHtml(row.new_product_name)}</span>` :
                            '<span class="text-gray-400">-</span>'));
                
                const description = row.aciklama ? 
                    escapeHtml(row.aciklama) : 
                    '<span class="text-gray-400 italic">Not girilmedi</span>';
                
                const userName = row.kullanici_isim || 'Sistem';
                
                const customerLink = customerId ? '' : 
                    `<a href="islemler.php?customer=${row.musteri_id}" class="text-primary-600 hover:text-primary-900 font-medium">${escapeHtml(row.musteri_isim)}</a>`;
                
                const customerCell = customerId ? '' : `<td>${customerLink}</td>`;
                
                html += `
                    <tr class="animate-fadeIn" style="animation-delay: ${0.3 + (index * 0.05)}s">
                        ${customerCell}
                        <td>${productName}</td>
                        <td>${formattedDate}</td>
                        <td class="font-medium">${amount} ₺</td>
                        <td>${typeBadge}</td>
                        <td>${description}</td>
                        <td>
                            <span class="text-sm text-gray-600 bg-gray-100 px-2 py-1 rounded-full" title="İşlemi ekleyen kullanıcı">
                                <i class="bi bi-person-fill mr-1 text-primary-500"></i>
                                ${escapeHtml(userName)}
                            </span>
                        </td>
                        <td class="text-right">
                            <div class="flex justify-end gap-2">
                                <a href="yazdir.php?id=${row.id}" class="btn btn-outline btn-sm" title="Yazdır" target="_blank">
                                    <i class="bi bi-printer"></i>
                                </a>
                                <a href="islemler.php?edit=${row.id}${customerId ? '&customer=' + customerId : ''}${currentPage > 1 ? '&page=' + currentPage : ''}" class="btn btn-outline btn-sm text-primary" title="Düzenle">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <a href="islemler.php?delete=${row.id}${customerId ? '&customer=' + customerId : ''}${currentPage > 1 ? '&page=' + currentPage : ''}" class="btn btn-outline btn-sm text-danger" title="Sil" onclick="return confirm('Bu işlemi silmek istediğinize emin misiniz?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                `;
            });
            
            tbody.innerHTML = html;
        }
        
        // Sayfalama güncelle
        function updatePagination(pagination) {
            const container = document.getElementById('pagination-container');
            
            if (pagination.total_pages <= 1) {
                container.innerHTML = '';
                return;
            }
            
            let html = '<nav class="inline-flex rounded-md shadow-sm" aria-label="Sayfalama">';
            
            // Önceki sayfa
            if (pagination.current_page > 1) {
                html += `<button onclick="performSearch(${pagination.current_page - 1})" class="px-3 py-2 border border-gray-300 rounded-l-md bg-white text-gray-700 hover:bg-gray-100">
                    <i class="bi bi-chevron-left"></i>
                </button>`;
            }
            
            // Sayfa numaraları
            for (let i = 1; i <= pagination.total_pages; i++) {
                if (i == 1 || i == pagination.total_pages || (i >= pagination.current_page - 1 && i <= pagination.current_page + 1)) {
                    const isActive = i === pagination.current_page;
                    const activeClass = isActive ? 'bg-primary-600 text-white border-primary-600' : 'bg-white text-gray-700 hover:bg-gray-100';
                    html += `<button onclick="performSearch(${i})" class="px-3 py-2 border border-gray-300 ${activeClass}">
                        ${i}
                    </button>`;
                } else if ((i == 2 && pagination.current_page > 3) || (i == pagination.total_pages - 1 && pagination.current_page < pagination.total_pages - 2)) {
                    html += '<span class="px-3 py-2 border border-gray-300 bg-white text-gray-700">...</span>';
                }
            }
            
            // Sonraki sayfa
            if (pagination.current_page < pagination.total_pages) {
                html += `<button onclick="performSearch(${pagination.current_page + 1})" class="px-3 py-2 border border-gray-300 rounded-r-md bg-white text-gray-700 hover:bg-gray-100">
                    <i class="bi bi-chevron-right"></i>
                </button>`;
            }
            
            html += '</nav>';
            container.innerHTML = html;
        }
        
        // Tablo başlığını güncelle
        function updateTableTitle(selectedCustomer) {
            const title = document.getElementById('table-title');
            if (selectedCustomer) {
                title.textContent = `${selectedCustomer.isim} - İşlem Geçmişi`;
            } else {
                title.textContent = 'Son İşlemler';
            }
        }
        
        // Hata mesajı göster
        function showError(message) {
            const tbody = document.getElementById('transactions-tbody');
            const customerId = document.getElementById('customer-filter') ? 
                document.getElementById('customer-filter').value : null;
            tbody.innerHTML = `
                <tr>
                    <td colspan="${customerId ? '7' : '8'}" class="text-center py-12 text-red-500">
                        <div class="flex flex-col items-center justify-center gap-3">
                            <i class="bi bi-exclamation-triangle text-5xl"></i>
                            <h4 class="text-lg font-medium">Hata</h4>
                            <p class="text-sm">${message}</p>
                        </div>
                    </td>
                </tr>
            `;
        }
        
        // HTML escape fonksiyonu
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // Event listeners
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    performSearch(1);
                }, 500);
            });
        }
        
        if (productSelect) {
            productSelect.addEventListener('change', () => performSearch(1));
        }
        
        if (typeSelect) {
            typeSelect.addEventListener('change', () => performSearch(1));
        }
        
        if (dateFromInput) {
            dateFromInput.addEventListener('change', () => performSearch(1));
        }
        
        if (dateToInput) {
            dateToInput.addEventListener('change', () => performSearch(1));
        }
        
        if (clearFiltersBtn) {
            clearFiltersBtn.addEventListener('click', function() {
                searchInput.value = '';
                productSelect.value = '';
                typeSelect.value = '';
                dateFromInput.value = '';
                dateToInput.value = '';
                performSearch(1);
            });
        }
        
        // Global fonksiyon olarak tanımla
        window.performSearch = performSearch;
    });

</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>