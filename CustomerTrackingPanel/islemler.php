<?php 
require_once __DIR__ . '/includes/auth.php'; 
require_login(); 

$pdo = get_pdo_connection();
$customerId = isset($_GET['customer']) ? (int)$_GET['customer'] : 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = (int)$_POST['customer_id'];
    $product_id = !empty($_POST['product_id']) ? (int)$_POST['product_id'] : null;
    $type = $_POST['type'] === 'debit' ? 'debit' : 'credit';
    $amount = (float)str_replace([',', ' '], ['.', ''], $_POST['amount']);
    $note = trim($_POST['note']);

    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare('INSERT INTO transactions (customer_id, product_id, type, amount, note) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$customer_id, $product_id, $type, $amount, $note]);
        if ($type === 'debit') {
            $pdo->prepare('UPDATE customers SET balance = balance + ? WHERE id = ?')->execute([$amount, $customer_id]);
        } else {
            $pdo->prepare('UPDATE customers SET balance = balance - ? WHERE id = ?')->execute([$amount, $customer_id]);
        }
        $pdo->commit();
        header('Location: islemler.php');
        exit;
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

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 5;
$offset = ($page - 1) * $perPage;

if ($customerId) {
    $countStmt = $pdo->prepare('SELECT COUNT(*) FROM transactions WHERE customer_id = ?');
    $countStmt->execute([$customerId]);
    $totalRows = (int)$countStmt->fetchColumn();
} else {
    $totalRows = (int)$pdo->query('SELECT COUNT(*) FROM transactions')->fetchColumn();
}
$totalPages = max(1, ceil($totalRows / $perPage));

try {
    if ($customerId) {
        $stmt = $pdo->prepare('SELECT t.*, c.name AS customer_name, p.name AS product_name FROM transactions t JOIN customers c ON c.id = t.customer_id LEFT JOIN products p ON p.id = t.product_id WHERE t.customer_id = ? ORDER BY t.created_at DESC LIMIT ? OFFSET ?');
        $stmt->bindValue(1, $customerId, PDO::PARAM_INT);
        $stmt->bindValue(2, $perPage, PDO::PARAM_INT);
        $stmt->bindValue(3, $offset, PDO::PARAM_INT);
        $stmt->execute();
    } else {
        $stmt = $pdo->prepare('SELECT t.*, c.name AS customer_name, p.name AS product_name FROM transactions t JOIN customers c ON c.id = t.customer_id LEFT JOIN products p ON p.id = t.product_id ORDER BY t.created_at DESC LIMIT ? OFFSET ?');
        $stmt->bindValue(1, $perPage, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
    }
} catch (PDOException $e) {
    if ($customerId) {
        $stmt = $pdo->prepare('SELECT t.*, c.name AS customer_name, p.name AS product_name FROM transactions t JOIN customers c ON c.id = t.customer_id LEFT JOIN products p ON p.id = t.product_id WHERE t.customer_id = ? ORDER BY t.created_at DESC');
        $stmt->execute([$customerId]);
    } else {
        $stmt = $pdo->query('SELECT t.*, c.name AS customer_name, p.name AS product_name FROM transactions t JOIN customers c ON c.id = t.customer_id LEFT JOIN products p ON p.id = t.product_id ORDER BY t.created_at DESC');
    }
}

?>

<div class="floating-element"></div>
<div class="floating-element"></div>
<div class="floating-element"></div>


<div class="container mx-auto px-4 py-6">
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
            </a>
        <?php endif; ?>
    </div>

    <div class="card-hover animate-fadeIn mb-6 shadow-lg">
        <div class="card-header flex items-center">
            <i class="bi bi-plus-circle mr-2 text-primary-600"></i>
            <h3 class="card-title">Yeni İşlem Ekle</h3>
        </div>
        <div class="p-5">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger mb-4 flex items-center">
                    <i class="bi bi-exclamation-triangle-fill mr-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="post" class="grid grid-cols-1 md:grid-cols-12 gap-5">
                <div class="md:col-span-3 col-span-1">
                    <label class="form-label flex items-center">
                        <i class="bi bi-person mr-2 text-primary-500"></i> Müşteri
                    </label>
                    <select name="customer_id" class="form-select" required>
                        <option value="">Müşteri Seçiniz</option>
                        <?php foreach ($customers as $c): ?>
                        <option value="<?php echo $c['id']; ?>" <?php echo $customerId === (int)$c['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($c['name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="md:col-span-3 col-span-1">
                    <label class="form-label flex items-center">
                        <i class="bi bi-box-seam mr-2 text-primary-500"></i> Ürün
                    </label>
                    <select name="product_id" class="form-select">
                        <option value="">Ürün Seçiniz (Opsiyonel)</option>
                        <?php foreach ($products as $product): ?>
                        <option value="<?php echo $product['id']; ?>">
                            <?php echo htmlspecialchars($product['name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
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
                    <input type="text" name="note" class="form-input" placeholder="İşlem açıklaması">
                </div>
                
                <div class="md:col-span-12 col-span-1 mt-2">
                    <button type="submit" class="btn btn-primary flex items-center shadow-sm hover:shadow-md transition-all" id="submitButton">
                        <i class="bi bi-plus-circle mr-2"></i> <span id="submitText">İşlem Ekle</span>
                    </button>
                </div>
            </form>
        </div>
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
                                    <a href="print.php?id=<?php echo $row['id']; ?>" class="btn btn-outline btn-sm" title="Yazdır">
                                        <i class="bi bi-printer"></i>
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
<?php require_once __DIR__ . '/includes/footer.php'; ?>