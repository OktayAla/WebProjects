<?php 
require_once __DIR__ . '/includes/auth.php'; 
require_login(); 

$pdo = get_pdo_connection();
$customerId = isset($_GET['customer']) ? (int)$_GET['customer'] : 0;

$editTransaction = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT t.*, c.name as customer_name FROM transactions t JOIN customers c ON t.customer_id = c.id WHERE t.id = ?');
    $stmt->execute([(int)$_GET['edit']]);
    $editTransaction = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo->beginTransaction();
    try {
        // Çoklu ürün ve tahsilat işlemleri
        $customer_id = (int)($_POST['customer_id'] ?? 0);
        if ($customer_id) {
            // Ürün satırlarını işle
            if (!empty($_POST['amount']) && is_array($_POST['amount'])) {
                foreach ($_POST['amount'] as $idx => $amount) {
                    $product_id = !empty($_POST['product_id'][$idx]) ? (int)$_POST['product_id'][$idx] : null;
                    $type = $_POST['type'][$idx] ?? 'debit';
                    $note = trim($_POST['note'][$idx] ?? '');
                    $amount = (float)str_replace([',', ' '], ['.', ''], $amount);
                    if ($amount > 0) {
                        $stmt = $pdo->prepare('INSERT INTO transactions (customer_id, product_id, type, amount, note) VALUES (?, ?, ?, ?, ?)');
                        $stmt->execute([$customer_id, $product_id, $type, $amount, $note]);
                        if ($type === 'debit') {
                            $pdo->prepare('UPDATE customers SET balance = balance + ? WHERE id = ?')->execute([$amount, $customer_id]);
                        }
                    }
                }
            }
            // Tahsilat işlemini işle
            if (!empty($_POST['payment_amount'])) {
                $payAmount = (float)str_replace([',', ' '], ['.', ''], $_POST['payment_amount']);
                $payNote = trim($_POST['payment_note'] ?? '');
                if ($payAmount > 0) {
                    $stmt = $pdo->prepare('INSERT INTO transactions (customer_id, type, amount, note) VALUES (?, ?, ?, ?)');
                    $stmt->execute([$customer_id, 'credit', $payAmount, $payNote]);
                    $pdo->prepare('UPDATE customers SET balance = balance - ? WHERE id = ?')->execute([$payAmount, $customer_id]);
                }
            }
            $pdo->commit();
            header('Location: islemler.php' . ($customerId ? '?customer=' . $customerId : ''));
            exit;
        }
        // İşlem güncelleme
        if (isset($_POST['action']) && $_POST['action'] === 'update_transaction' && isset($_POST['transaction_id'])) {
            $transaction_id = (int)$_POST['transaction_id'];
            $product_id = !empty($_POST['product_id']) ? (int)$_POST['product_id'] : null;
            $type = $_POST['type'] === 'debit' ? 'debit' : 'credit';
            $amount = (float)str_replace([',', ' '], ['.', ''], $_POST['amount']);
            $note = trim($_POST['note']);
            
            $stmt = $pdo->prepare('UPDATE transactions SET product_id = ?, type = ?, amount = ?, note = ? WHERE id = ?');
            $stmt->execute([$product_id, $type, $amount, $note, $transaction_id]);
            
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
        // Yeni işlem ekleme
        else {
            $customer_id = (int)$_POST['customer_id'];
            $product_id = !empty($_POST['product_id']) ? (int)$_POST['product_id'] : null;
            $type = $_POST['type'] === 'debit' ? 'debit' : 'credit';
            $amount = (float)str_replace([',', ' '], ['.', ''], $_POST['amount']);
            $note = trim($_POST['note']);

            $stmt = $pdo->prepare('INSERT INTO transactions (customer_id, product_id, type, amount, note) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$customer_id, $product_id, $type, $amount, $note]);
            if ($type === 'debit') {
                $pdo->prepare('UPDATE customers SET balance = balance + ? WHERE id = ?')->execute([$amount, $customer_id]);
            } else {
                $pdo->prepare('UPDATE customers SET balance = balance - ? WHERE id = ?')->execute([$amount, $customer_id]);
            }
            $pdo->commit();
            header('Location: islemler.php' . ($customerId ? '?customer=' . $customerId : ''));
            exit;
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = 'İşlem başarısız: ' . $e->getMessage();
    }
}

require_once __DIR__ . '/includes/header.php'; 

$customers = $pdo->query('SELECT id, name FROM customers ORDER BY name ASC')->fetchAll();
$products = $pdo->query('SELECT id, name FROM products ORDER BY name ASC')->fetchAll();
$selectedCustomer = null;
if ($customerId) {
    $st = $pdo->prepare('SELECT * FROM customers WHERE id = ?');
    $st->execute([$customerId]);
    $selectedCustomer = $st->fetch();
}

// Filtreleme parametreleri
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 5;
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
    $conditions[] = 'customer_id = ?';
    $params[] = $customerId;
}

if ($search) {
    $conditions[] = '(c.name LIKE ? OR t.note LIKE ?)';
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($productFilter) {
    $conditions[] = 't.product_id = ?';
    $params[] = $productFilter;
}

if ($typeFilter) {
    $conditions[] = 't.type = ?';
    $params[] = $typeFilter;
}

if ($dateFrom) {
    $conditions[] = 't.created_at >= ?';
    $params[] = $dateFrom . ' 00:00:00';
}

if ($dateTo) {
    $conditions[] = 't.created_at <= ?';
    $params[] = $dateTo . ' 23:59:59';
}

// SQL sorgusu oluşturma
$whereClause = '';
if (!empty($conditions)) {
    $whereClause = 'WHERE ' . implode(' AND ', $conditions);
}

// Toplam kayıt sayısını hesaplama
$countSql = "SELECT COUNT(*) FROM transactions t JOIN customers c ON c.id = t.customer_id $whereClause";
$countStmt = $pdo->prepare($countSql);
$countStmt->execute($params);
$totalRows = (int)$countStmt->fetchColumn();
$totalPages = max(1, ceil($totalRows / $perPage));

try {
    $sql = "SELECT t.*, c.name AS customer_name, p.name AS product_name 
           FROM transactions t 
           JOIN customers c ON c.id = t.customer_id 
           LEFT JOIN products p ON p.id = t.product_id 
           $whereClause 
           ORDER BY t.created_at DESC 
           LIMIT ? OFFSET ?";
    
    $stmt = $pdo->prepare($sql);
    $paramIndex = 1;
    
    foreach ($params as $param) {
        $stmt->bindValue($paramIndex++, $param);
    }
    
    $stmt->bindValue($paramIndex++, $perPage, PDO::PARAM_INT);
    $stmt->bindValue($paramIndex++, $offset, PDO::PARAM_INT);
    $stmt->execute();
} catch (PDOException $e) {
    $sql = "SELECT t.*, c.name AS customer_name, p.name AS product_name 
           FROM transactions t 
           JOIN customers c ON c.id = t.customer_id 
           LEFT JOIN products p ON p.id = t.product_id 
           $whereClause 
           ORDER BY t.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
}

?>

<div class="floating-element"></div>
<div class="floating-element"></div>
<div class="floating-element"></div>

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
                    } else {
                        echo 'İşlem başarıyla eklendi.';
                    }
                    ?>
                </p>
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
                    <span class="font-medium"><?php echo htmlspecialchars($selectedCustomer['name']); ?></span> müşterisi için işlemler
                <?php else: ?>
                    Tüm müşteriler için işlem ekle ve geçmişi görüntüle
                <?php endif; ?>
            </p>
        </div>
        <?php if ($selectedCustomer): ?>
            <a href="musteri_rapor.php?customer=<?php echo $customerId; ?>" class="btn btn-secondary flex items-center">
                <i class="bi bi-file-earmark-bar-graph mr-2"></i> Müşteri Raporu
    <?php foreach ($customers as $c): ?>
    <option value="<?php echo htmlspecialchars($c['name']); ?>">
    <?php endforeach; ?>
</datalist>
<input type="hidden" name="customer_id" id="customerIdHidden" value="<?php echo $customerId; ?>">
                </div>
                <div class="md:col-span-3 col-span-1">
                    <label class="form-label flex items-center">
                        <i class="bi bi-box-seam mr-2 text-primary-500"></i> Ürün
                    </label>
                    <input list="productList" name="product_input" class="form-input" placeholder="Ürün Seçiniz veya Yazınız (Opsiyonel)">
<datalist id="productList">
    <?php foreach ($products as $product): ?>
    <option value="<?php echo htmlspecialchars($product['name']); ?>">
    <?php endforeach; ?>
</datalist>
<input type="hidden" name="product_id" id="productIdHidden">
                </div>
                <div class="md:col-span-2 col-span-1">
                    <label class="form-label flex items-center">
                        <i class="bi bi-arrow-left-right mr-2 text-primary-500"></i> İşlem Türü
                    </label>
                    <select name="type" class="form-select" id="transactionType">
                        <option value="debit" data-color="danger">Borç Ekle</option>
                        <option value="credit" data-color="success">Tahsilat Ekle</option>
                    </select>
                </div>
                
                <div class="md:col-span-2 col-span-1">
                    <label class="form-label flex items-center">
                        <i class="bi bi-currency-exchange mr-2 text-primary-500"></i> Tutar (₺)
                    </label>
                    <input type="text" name="amount" class="form-input" placeholder="0,00" required id="amountInput">
                </div>
                
                <div class="md:col-span-2 col-span-1">
                    <label class="form-label flex items-center">
                        <i class="bi bi-chat-left-text mr-2 text-primary-500"></i> Açıklama
                    </label>
                    <input type="text" name="note" class="form-input" id="noteInput" placeholder="İşlem açıklaması">
                </div>
                
                <div class="md:col-span-12 col-span-1 mt-2">
                    <button type="submit" class="btn btn-primary flex items-center shadow-sm hover:shadow-md transition-all" id="submitButton">
                        <i class="bi bi-plus-circle mr-2"></i> <span id="submitText">İşlem Ekle</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

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
                            <?php echo htmlspecialchars($product['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="type" class="form-label">İşlem Türü</label>
                <select id="type" name="type" class="form-select">
                    <option value="">Tümü</option>
                    <option value="debit" <?php echo $typeFilter === 'debit' ? 'selected' : ''; ?>>Borç</option>
                    <option value="credit" <?php echo $typeFilter === 'credit' ? 'selected' : ''; ?>>Tahsilat</option>
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

    <div class="card-hover animate-fadeIn shadow-lg" style="animation-delay: 0.2s">
        <div class="card-header">
            <div class="flex justify-between items-center">
                <h3 class="card-title flex items-center">
                    <i class="bi bi-clock-history mr-2 text-primary-600"></i>
                    <?php if ($customerId && $selectedCustomer): ?>
                        <?php echo htmlspecialchars($selectedCustomer['name']); ?> - İşlem Geçmişi
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
                            <th><i class="bi bi-person-badge mr-1 text-primary-500"></i> Müşteri</th>
                            <th><i class="bi bi-box-seam mr-1 text-primary-500"></i> Ürün</th>
                            <th><i class="bi bi-calendar-date mr-1 text-primary-500"></i> Tarih</th>
                            <th><i class="bi bi-currency-exchange mr-1 text-primary-500"></i> Tutar (₺)</th>
                            <th><i class="bi bi-arrow-left-right mr-1 text-primary-500"></i> Tür</th>
                            <th><i class="bi bi-chat-left-text mr-1 text-primary-500"></i> Açıklama</th>
                            <th class="text-right"><i class="bi bi-gear-fill text-primary-500"></i> İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $hasTransactions = false;
                            $index = 0;
                            foreach ($stmt as $row):
                            $hasTransactions = true;
                            $index++;
                        ?>
                        <tr class="animate-fadeIn" style="animation-delay: <?php echo 0.3 + ($index * 0.05); ?>s">
                            <td>
                                <?php if (!$customerId): ?>
                                <a href="transactions.php?customer=<?php echo $row['customer_id']; ?>" class="text-primary-600 hover:text-primary-900 font-medium">
                                    <?php echo htmlspecialchars($row['customer_name']); ?>
                                </a>
                                <?php else: ?>
                                    <?php echo htmlspecialchars($row['customer_name']); ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (isset($row['product_name']) && $row['product_name']): ?>
                                    <span class="badge badge-outline"><?php echo htmlspecialchars($row['product_name']); ?></span>
                                <?php else: ?>
                                    <span class="text-gray-400">-</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('d.m.Y H:i', strtotime($row['created_at'])); ?></td>
                            <td class="font-medium"><?php echo number_format($row['amount'], 2, ',', '.'); ?> ₺</td>
                            <td>
                                <?php if ($row['type'] === 'debit'): ?>
                                <span class="badge-debit flex items-center w-fit"><i class="bi bi-arrow-down-right mr-1"></i> Borç</span>
                                <?php else: ?>
                                <span class="badge-credit flex items-center w-fit"><i class="bi bi-arrow-up-right mr-1"></i> Tahsilat</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($row['note']): ?>
                                    <?php echo htmlspecialchars($row['note']); ?>
                                <?php else: ?>
                                    <span class="text-gray-400 italic">Not girilmedi</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="yazdir.php?id=<?php echo $row['id']; ?>" class="btn btn-outline btn-sm" title="Yazdır" target="_blank">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                    <a href="islemler.php?edit=<?php echo $row['id']; ?>" class="btn btn-outline btn-sm text-primary" title="Düzenle">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="#" class="btn btn-outline btn-sm text-danger" title="Sil" onclick="return confirm('Bu işlemi silmek istediğinize emin misiniz?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (!$hasTransactions): ?>
                        <tr>
                            <td colspan="8" class="text-center py-12 text-gray-500">
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

    <?php if ($totalPages > 1): ?>
    <div class="flex justify-center mt-6">
        <nav class="inline-flex rounded-md shadow-sm" aria-label="Sayfalama">
            <?php if ($page > 1): ?>
                <a href="transactions.php<?php echo $customerId ? '?customer=' . $customerId . '&' : '?'; ?>page=<?php echo $page-1; ?>"
                   class="px-3 py-2 border border-gray-300 rounded-l-md bg-white text-gray-700 hover:bg-gray-100">
                    <i class="bi bi-chevron-left"></i>
                </a>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php if ($i == 1 || $i == $totalPages || ($i >= $page - 1 && $i <= $page + 1)): ?>
                    <a href="transactions.php<?php echo $customerId ? '?customer=' . $customerId . '&' : '?'; ?>page=<?php echo $i; ?>"
                       class="px-3 py-2 border border-gray-300 <?php echo $i == $page ? 'bg-primary-600 text-white border-primary-600' : 'bg-white text-gray-700 hover:bg-gray-100'; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php elseif (($i == 2 && $page > 3) || ($i == $totalPages - 1 && $page < $totalPages - 2)): ?>
                    <span class="px-3 py-2 border border-gray-300 bg-white text-gray-700">...</span>
                <?php endif; ?>
            <?php endfor; ?>
            
            <?php if ($page < $totalPages): ?>
                <a href="transactions.php<?php echo $customerId ? '?customer=' . $customerId . '&' : '?'; ?>page=<?php echo $page+1; ?>"
                   class="px-3 py-2 border border-gray-300 rounded-r-md bg-white text-gray-700 hover:bg-gray-100">
                    <i class="bi bi-chevron-right"></i>
                </a>
            <?php endif; ?>
        </nav>
    </div>
    <?php endif; ?>
</div>

<script>
// İşlem türü değiştiğinde buton metnini güncelle
document.addEventListener('DOMContentLoaded', function() {
    const transactionType = document.getElementById('transactionType');
    const submitText = document.getElementById('submitText');
    const submitButton = document.getElementById('submitButton');
    const amountInput = document.getElementById('amountInput');
    
    // İşlem türü değiştiğinde buton metnini ve rengini güncelle
    if (transactionType) {
        transactionType.addEventListener('change', function() {
            const selectedOption = transactionType.options[transactionType.selectedIndex];
            const colorClass = selectedOption.dataset.color;
            
            // Buton metnini güncelle
            submitText.textContent = selectedOption.textContent;
            
            // Buton rengini güncelle
            submitButton.className = submitButton.className.replace(/btn-(primary|success|danger|warning|info)/, '');
            submitButton.classList.add(`btn-${colorClass}`);
            
            // Tutar alanına odaklan
            amountInput.focus();
        });
    }
    
    // Ürün seçimi değiştiğinde
    const productSelect = document.querySelector('select[name="product_id"]');
    if (productSelect) {
        productSelect.addEventListener('change', function() {
            // Ürün seçildiğinde tutar alanına odaklan
            if (productSelect.value) {
                amountInput.focus();
            }
        });
    }
    
    // Tutar alanı için para formatı
    if (amountInput) {
        amountInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/[^\d,]/g, '');
            value = value.replace(',', '.');
            if (value.includes('.')) {
                const parts = value.split('.');
                if (parts[1].length > 2) {
                    parts[1] = parts[1].substring(0, 2);
                }
                value = parts.join('.');
            }
            e.target.value = value;
        });
    }
    
    // Form gönderildiğinde buton durumunu değiştir
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function() {
            const submitButton = document.getElementById('submitButton');
            if (submitButton) {
                // Buton metnini değiştir ve yükleniyor simgesi ekle
                submitButton.innerHTML = '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span> İşleniyor...';
                submitButton.disabled = true;
            }
        });
    }
});
</script>

// Manual entry logic for customer and product fields
const customerInput = document.querySelector('input[name="customer_input"]');
const customerIdHidden = document.getElementById('customerIdHidden');
const productInput = document.querySelector('input[name="product_input"]');
const productIdHidden = document.getElementById('productIdHidden');
const noteInput = document.getElementById('noteInput');
const customers = <?php echo json_encode($customers); ?>;
const products = <?php echo json_encode($products); ?>;

if (customerInput && customerIdHidden) {
    customerInput.addEventListener('input', function() {
        const found = customers.find(c => c.name === this.value);
        customerIdHidden.value = found ? found.id : '';
    });
}
if (productInput && productIdHidden) {
    productInput.addEventListener('input', function() {
        const found = products.find(p => p.name === this.value);
        productIdHidden.value = found ? found.id : '';
    });
}

// On form submit, if customer or product not found, add to note
const transactionForm = document.getElementById('transactionForm');
if (transactionForm) {
    transactionForm.addEventListener('submit', function(e) {
        let extraNote = '';
        if (customerInput && !customerIdHidden.value) {
            extraNote += 'Müşteri: ' + customerInput.value + '. ';
        }
        if (productInput && !productIdHidden.value && productInput.value) {
            extraNote += 'Ürün: ' + productInput.value + '. ';
        }
        if (extraNote) {
            noteInput.value = (noteInput.value ? noteInput.value + ' | ' : '') + extraNote;
        }
    });
}

<!-- Transaction Edit Modal -->
<div id="editTransactionModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-50 flex items-center justify-center <?php echo $editTransaction ? '' : 'hidden'; ?>">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 animate-fadeIn">
        <div class="p-4 border-b border-gray-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <i class="bi bi-pencil-square mr-2 text-primary-600"></i> İşlem Düzenle
            </h3>
            <button type="button" class="text-gray-400 hover:text-gray-500 close-modal">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <form id="editTransactionForm" method="POST" action="">
            <div class="p-4">
                <input type="hidden" name="transaction_id" id="edit_transaction_id" value="<?php echo $editTransaction ? (int)$editTransaction['id'] : 0; ?>">
                <input type="hidden" name="action" value="update_transaction">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="form-label flex items-center">
                            <i class="bi bi-person mr-2 text-primary-500"></i> Müşteri
                        </label>
                        <input type="text" id="edit_customer" class="form-input bg-gray-100" readonly value="<?php echo htmlspecialchars($editTransaction['customer_name']); ?>">
                    </div>
                    
                    <div>
                        <label class="form-label flex items-center">
                            <i class="bi bi-box-seam mr-2 text-primary-500"></i> Ürün
                        </label>
                        <select name="product_id" id="edit_product" class="form-select">
                            <option value="">Ürün Seçiniz (Opsiyonel)</option>
                            <?php foreach ($products as $product): ?>
                            <option value="<?php echo $product['id']; ?>" <?php echo $editTransaction && $editTransaction['product_id'] == $product['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($product['name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label class="form-label flex items-center">
                            <i class="bi bi-arrow-left-right mr-2 text-primary-500"></i> İşlem Türü
                        </label>
                        <select name="type" id="edit_type" class="form-select">
                            <option value="debit" data-color="danger" <?php echo $editTransaction && $editTransaction['type'] === 'debit' ? 'selected' : ''; ?>>Borç</option>
                            <option value="credit" data-color="success" <?php echo $editTransaction && $editTransaction['type'] === 'credit' ? 'selected' : ''; ?>>Tahsilat</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="form-label flex items-center">
                            <i class="bi bi-currency-exchange mr-2 text-primary-500"></i> Tutar (₺)
                        </label>
                        <input type="text" name="amount" id="edit_amount" class="form-input" placeholder="0,00" required value="<?php echo htmlspecialchars(number_format($editTransaction['amount'], 2, ',', '.')); ?>">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="form-label flex items-center">
                            <i class="bi bi-chat-left-text mr-2 text-primary-500"></i> Açıklama
                        </label>
                        <input type="text" name="note" id="edit_note" class="form-input" placeholder="İşlem açıklaması" value="<?php echo htmlspecialchars($editTransaction['note']); ?>">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('editTransactionModal');
    const closeButtons = document.querySelectorAll('.close-modal');

    const closeModal = () => {
        modal.classList.add('hidden');
        // Clean up the URL
        const url = new URL(window.location);
        url.searchParams.delete('edit');
        window.history.replaceState({}, document.title, url);
    };

    closeButtons.forEach(button => {
        button.addEventListener('click', closeModal);
    });

    // Close modal when clicking outside of it
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });

    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === "Escape" && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>