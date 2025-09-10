<?php
require_once __DIR__ . '/includes/auth.php';
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

            // Bakiye güncellemesi için eski işlemi alalım
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

            // Çoklu ürün işleme
            $products = $_POST['products'] ?? [];
            $totalDebit = 0;
            $totalCredit = 0;

            foreach ($products as $productData) {
                if (!empty($productData['amount'])) {
                    $urun_id = !empty($productData['product_id']) ? (int)$productData['product_id'] : null;
                    $odeme_tipi = $productData['type'];
                    $miktar = (float)str_replace([',', ' '], ['.', ''], $productData['amount']);
                    $urun_notu = !empty($productData['note']) ? trim($productData['note']) : '';
                    
                    // Eğer ürün ID 0 ise ve yeni ürün adı varsa, yeni ürün oluştur
                    if ($urun_id === 0 && !empty($productData['new_product_name'])) {
                        $newProductName = trim($productData['new_product_name']);
                        
                        $stmt = $pdo->prepare('INSERT INTO urunler (isim, fiyat) VALUES (?, 0)');
                        $stmt->execute([$newProductName]);
                        $urun_id = $pdo->lastInsertId();
                        
                        // Yeni ürün adını açıklamaya ekle
                        if (!empty($urun_notu)) {
                            $urun_notu = "Yeni ürün: " . $newProductName . " - " . $urun_notu;
                        } else {
                            $urun_notu = "Yeni ürün: " . $newProductName;
                        }
                    }

                    // İşlemi eklerken user_id'yi de kaydediyoruz
                    $stmt = $pdo->prepare('INSERT INTO islemler (musteri_id, urun_id, odeme_tipi, miktar, aciklama, kullanici_id) VALUES (?, ?, ?, ?, ?, ?)');
                    $stmt->execute([$musteri_id, $urun_id, $odeme_tipi, $miktar, $urun_notu, $currentUserId]);

                    if ($odeme_tipi === 'borc') {
                        $totalDebit += $miktar;
                    } else {
                        $totalCredit += $miktar;
                    }
                }
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
    $conditions[] = '(m.isim LIKE ? OR i.aciklama LIKE ?)';
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

// SQL sorgusu oluşturma
$whereClause = '';
if (!empty($conditions)) {
    $whereClause = 'WHERE ' . implode(' AND ', $conditions);
}

// Toplam kayıt sayısını hesaplama
$countSql = "SELECT COUNT(*) FROM islemler i JOIN musteriler m ON m.id = i.musteri_id $whereClause";
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$totalRows = (int)$countStmt->fetchColumn();
$totalPages = max(1, ceil($totalRows / $perPage));

// Ana sorgu - user bilgisini de joinliyoruz (users.name kullanıyoruz)
$sql = "SELECT i.*, m.isim AS musteri_isim, u.isim AS urun_isim, k.isim AS kullanici_isim
       FROM islemler i
       JOIN musteriler m ON m.id = i.musteri_id
       LEFT JOIN urunler u ON u.id = i.urun_id
       LEFT JOIN kullanicilar k ON k.id = i.kullanici_id
       $whereClause
       ORDER BY i.olusturma_zamani DESC
       LIMIT ? OFFSET ?";

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
                                <input type="text" id="customer-search" class="form-input" placeholder="Müşteri ara veya yeni müşteri adı yazın..." autocomplete="off">
                                <input type="hidden" name="customer_id" id="customer_id" required>
                                <input type="hidden" name="new_customer_name" id="new_customer_name">
                                <input type="hidden" name="new_customer_phone" id="new_customer_phone">
                                <div id="customer-suggestions" class="absolute z-10 w-full bg-white border border-gray-300 rounded-md shadow-lg hidden max-h-60 overflow-y-auto">
                                    <!-- Öneriler buraya yüklenecek -->
                                </div>
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

                        <div class="md:col-span-3 col-span-1">
                            <label class="form-label flex items-center">
                                <i class="bi bi-currency-exchange mr-2 text-primary-500"></i> Tutar (₺)
                            </label>
                            <input type="text" name="products[0][amount]" class="form-input amount-input" placeholder="0,00" required>
                        </div>

                        <div class="md:col-span-2 col-span-1">
                            <label class="form-label flex items-center">
                                <i class="bi bi-chat-left-text mr-2 text-primary-500"></i> Ürün Notu
                            </label>
                            <input type="text" name="products[0][note]" class="form-input" placeholder="Bu ürün için not">
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
            <form action="" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <?php if ($customerId): ?>
                    <input type="hidden" name="customer" value="<?php echo $customerId; ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="search" class="form-label">Arama</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                            <i class="bi bi-search text-gray-400"></i>
                        </span>
                        <input type="text" id="search" name="search" class="form-input pl-10" placeholder="Müşteri adı veya not..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="product" class="form-label">Ürün</label>
                    <select id="product" name="product" class="form-select">
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
                    <select id="type" name="type" class="form-select">
                        <option value="">Tümü</option>
                        <option value="borc" <?php echo $typeFilter === 'borc' ? 'selected' : ''; ?>>Borç</option>
                        <option value="tahsilat" <?php echo $typeFilter === 'tahsilat' ? 'selected' : ''; ?>>Tahsilat</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div class="form-group">
                        <label for="date_from" class="form-label">Başlangıç Tarihi</label>
                        <input type="date" id="date_from" name="date_from" class="form-input" value="<?php echo $dateFrom; ?>">
                    </div>
                    <div class="form-group">
                        <label for="date_to" class="form-label">Bitiş Tarihi</label>
                        <input type="date" id="date_to" name="date_to" class="form-input" value="<?php echo $dateTo; ?>">
                    </div>
                </div>

                <div class="col-span-full flex items-center justify-end gap-2 mt-2">
                    <a href="islemler.php<?php echo $customerId ? '?customer=' . $customerId : ''; ?>" class="btn btn-outline">
                        <i class="bi bi-x-circle mr-1"></i> Temizle
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-filter mr-1"></i> Filtrele
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- İşlem Geçmişi -->
    <div class="card-hover animate-fadeIn shadow-lg" style="animation-delay: 0.2s">
        <div class="card-header">
            <div class="flex justify-between items-center">
                <h3 class="card-title flex items-center">
                    <i class="bi bi-clock-history mr-2 text-primary-600"></i>
                    <?php if ($customerId && $selectedCustomer): ?>
                        <?php echo htmlspecialchars($selectedCustomer['isim']); ?> - İşlem Geçmişi
                    <?php else: ?>
                        Son İşlemler
                    <?php endif; ?>
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
                    <tbody>
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
                                    <?php if (isset($row['urun_isim']) && $row['urun_isim']): ?>
                                        <span class="badge badge-outline"><?php echo htmlspecialchars($row['urun_isim']); ?></span>
                                    <?php else: ?>
                                        <span class="text-gray-400">-</span>
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

    <!-- Sayfalama -->
    <?php if ($totalPages > 1): ?>
        <div class="flex justify-center mt-6">
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
        </div>
    <?php endif; ?>
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
                    <select name="products[${productCounter}][product_id]" class="form-select product-select" required>
                        <option value="">Ürün Seçiniz</option>
                        <?php foreach ($products as $product): ?>
                        <option value="<?php echo $product['id']; ?>">
                            <?php echo htmlspecialchars($product['isim']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="md:col-span-2 col-span-1">
                    <select name="products[${productCounter}][type]" class="form-select type-select">
                        <option value="borc">Borç</option>
                        <option value="tahsilat">Tahsilat</option>
                    </select>
                </div>
                
                <div class="md:col-span-3 col-span-1">
                    <input type="text" name="products[${productCounter}][amount]" class="form-input amount-input" placeholder="0,00" required>
                </div>
                
                <div class="md:col-span-2 col-span-1">
                    <input type="text" name="products[${productCounter}][note]" class="form-input" placeholder="Bu ürün için not">
                </div>
                
                <div class="md:col-span-1 col-span-1 flex items-end">
                    <button type="button" class="btn btn-outline text-red-500 remove-product" title="Kaldır">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            `;

                productsContainer.appendChild(newRow);
                productCounter++;

                // Yeni eklenen satırdaki amount inputunu ayarla
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

        // Tutar alanları için para formatı
        function setupAmountInputs() {
            const amountInputs = document.querySelectorAll('.amount-input');
            amountInputs.forEach(input => {
                input.addEventListener('input', function (e) {
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
                });
            });
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
        
        // Müşteri arama özelliği
        const customerSearch = document.getElementById('customer-search');
        const customerSuggestions = document.getElementById('customer-suggestions');
        const customerIdInput = document.getElementById('customer_id');
        
        if (customerSearch) {
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
                        } else {
                            // Eğer öneri yoksa, yeni müşteri olarak ekle
                            customerIdInput.value = '0';
                            document.getElementById('new_customer_name').value = query;
                            customerSuggestions.classList.add('hidden');
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
            
            // Dışarı tıklandığında önerileri gizle
            document.addEventListener('click', function(e) {
                if (!productSearchInput.contains(e.target) && !productSuggestions.contains(e.target)) {
                    productSuggestions.classList.add('hidden');
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
    });

    // Otomatik filtreleme için form gönderimini yönet
    document.addEventListener('DOMContentLoaded', function() {
        const filterForm = document.querySelector('form[action=""][method="GET"]');
        if (filterForm) {
            // Tüm filtre alanlarını seç
            const filterInputs = filterForm.querySelectorAll('input, select');
            
            // Her bir filtre alanı için değişiklik dinleyicisi ekle
            filterInputs.forEach(input => {
                // Tarih alanları için 'change' olayını kullan
                if (input.type === 'date') {
                    input.addEventListener('change', submitFilterForm);
                } 
                // Arama kutusu için 'input' olayını kullan (yazarken arama yapmak için)
                else if (input.id === 'search') {
                    let searchTimer;
                    input.addEventListener('input', function() {
                        clearTimeout(searchTimer);
                        searchTimer = setTimeout(() => {
                            submitFilterForm();
                        }, 500); // 500ms bekleme süresi
                    });
                }
                // Diğer tüm seçim alanları için 'change' olayını kullan
                else {
                    input.addEventListener('change', submitFilterForm);
                }
            });
            
            // Formu gönderen yardımcı fonksiyon
            function submitFilterForm() {
                // Sayfa numarasını sıfırla (yeni bir arama yapıldığında 1. sayfadan başlasın)
                const pageInput = document.querySelector('input[name="page"]');
                if (pageInput) {
                    pageInput.value = 1;
                }
                filterForm.submit();
            }
        }
    });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>