<?php 
require_once __DIR__ . '/includes/auth.php'; 
require_login(); 

$pdo = get_pdo_connection();
$customerId = isset($_GET['customer']) ? (int)$_GET['customer'] : 0;

// Add transaction
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
        header('Location: transactions.php?customer=' . $customer_id);
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = 'İşlem başarısız: ' . $e->getMessage();
    }
}

require_once __DIR__ . '/includes/header.php'; 

$customers = $pdo->query('SELECT id, name FROM customers ORDER BY name ASC')->fetchAll();
$selectedCustomer = null;
if ($customerId) {
    $st = $pdo->prepare('SELECT * FROM customers WHERE id = ?');
    $st->execute([$customerId]);
    $selectedCustomer = $st->fetch();
}
?>

<!-- Floating background elements -->
<div class="floating-element"></div>
<div class="floating-element"></div>
<div class="floating-element"></div>

<div class="dashboard-header">
    <div class="container mx-auto px-4">
        <h1 class="dashboard-title">İşlem Yönetimi</h1>
        <p class="dashboard-subtitle">Yeni işlem ekleme ve işlem geçmişi</p>
    </div>
</div>

<div class="container mx-auto px-4 py-6">
    <!-- Breadcrumb -->
    <div class="flex items-center space-x-2 text-sm text-gray-500 mb-6">
        <a href="index.php" class="hover:text-primary-600 transition-colors duration-200">Panel</a>
        <span class="text-gray-400">/</span>
        <span class="text-gray-700 font-medium">İşlemler</span>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">İşlemler</h2>
            <p class="text-sm text-gray-600 mt-1">Yeni işlem ekle ve geçmişi görüntüle</p>
        </div>
        <a href="customers.php" class="btn btn-outline flex items-center">
            <i class="bi bi-people mr-2"></i> Müşteriler
        </a>
    </div>

    <!-- Add Transaction Form -->
    <div class="card-hover animate-fadeIn mb-6">
        <div class="card-header">
            <h3 class="card-title">Yeni İşlem Ekle</h3>
        </div>
        <div class="p-4">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger mb-4"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="post" class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <div class="md:col-span-3 col-span-1">
                    <label class="form-label">Müşteri</label>
                    <select name="customer_id" class="form-select" required>
                        <option value="">Seçiniz</option>
                        <?php foreach ($customers as $c): ?>
                        <option value="<?php echo $c['id']; ?>" <?php echo $customerId === (int)$c['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($c['name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="md:col-span-3 col-span-1">
                    <label class="form-label">Ürün</label>
                    <select name="product_id" class="form-select">
                        <option value="">Ürün Seçiniz</option>
                        <?php 
                        $products = $pdo->query('SELECT id, name FROM products ORDER BY name ASC')->fetchAll();
                        foreach ($products as $product): 
                        ?>
                        <option value="<?php echo $product['id']; ?>">
                            <?php echo htmlspecialchars($product['name']); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="md:col-span-2 col-span-1">
                    <label class="form-label">Tür</label>
                    <select name="type" class="form-select">
                        <option value="debit">Borç</option>
                        <option value="credit">Tahsilat</option>
                    </select>
                </div>
                
                <div class="md:col-span-2 col-span-1">
                    <label class="form-label">Tutar (₺)</label>
                    <input type="text" name="amount" class="form-input" placeholder="0,00" required>
                </div>
                
                <div class="md:col-span-2 col-span-1">
                    <label class="form-label">Açıklama</label>
                    <input type="text" name="note" class="form-input" placeholder="İşlem açıklaması">
                </div>
                
                <div class="md:col-span-12 col-span-1">
                    <button type="submit" class="btn btn-primary flex items-center">
                        <i class="bi bi-plus-lg mr-2"></i> Ekle
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Transactions List -->
    <div class="card-hover animate-fadeIn" style="animation-delay: 0.2s">
        <div class="card-header">
            <div class="flex justify-between items-center">
                <h3 class="card-title">
                    <?php if ($customerId && $selectedCustomer): ?>
                        <?php echo htmlspecialchars($selectedCustomer['name']); ?> - İşlem Geçmişi
                    <?php else: ?>
                        Tüm İşlemler
                    <?php endif; ?>
                </h3>
                <?php if ($customerId && $selectedCustomer): ?>
                <a href="customer_report.php?customer=<?php echo $customerId; ?>" class="btn btn-outline btn-sm">
                    <i class="bi bi-file-earmark-text mr-2"></i> Rapor
                </a>
                <?php endif; ?>
            </div>
        </div>
        <div class="p-0">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Müşteri</th>
                            <th>Ürün</th>
                            <th>Tarih</th>
                            <th>Tutar (₺)</th>
                            <th>Tür</th>
                            <th>Açıklama</th>
                            <th class="text-right">İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            try {
                                if ($customerId) {
                                    $stmt = $pdo->prepare('SELECT t.*, c.name AS customer_name, p.name AS product_name FROM transactions t JOIN customers c ON c.id = t.customer_id LEFT JOIN products p ON p.id = t.product_id WHERE t.customer_id = ? ORDER BY t.created_at DESC');
                                    $stmt->execute([$customerId]);
                                } else {
                                    $stmt = $pdo->query('SELECT t.*, c.name AS customer_name, p.name AS product_name FROM transactions t JOIN customers c ON c.id = t.customer_id LEFT JOIN products p ON p.id = t.product_id ORDER BY t.created_at DESC');
                                }
                            } catch (PDOException $e) {
                                // Eğer id kolonu yoksa, eski sorguyu kullan
                                if ($customerId) {
                                    $stmt = $pdo->prepare('SELECT t.*, c.name AS customer_name FROM transactions t JOIN customers c ON c.id = t.customer_id WHERE t.customer_id = ? ORDER BY t.created_at DESC');
                                    $stmt->execute([$customerId]);
                                } else {
                                    $stmt = $pdo->query('SELECT t.*, c.name AS customer_name FROM transactions t JOIN customers c ON c.id = t.customer_id ORDER BY t.created_at DESC');
                                }
                            }
                            $hasTransactions = false;
                            $index = 0;
                            foreach ($stmt as $row):
                            $hasTransactions = true;
                            $index++;
                        ?>
                        <tr class="animate-fadeIn" style="animation-delay: <?php echo 0.3 + ($index * 0.05); ?>s">
                            <td><?php echo $row['id']; ?></td>
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
                            <td class="font-medium"><?php echo number_format($row['amount'], 2, ',', '.'); ?></td>
                            <td>
                                <?php if ($row['type'] === 'debit'): ?>
                                <span class="badge-debit">Borç</span>
                                <?php else: ?>
                                <span class="badge-credit">Tahsilat</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['note']); ?></td>
                            <td class="text-right">
                                <a href="print.php?id=<?php echo $row['id']; ?>" class="btn btn-outline btn-sm">
                                    <i class="bi bi-printer mr-1"></i> Yazdır
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (!$hasTransactions): ?>
                        <tr>
                            <td colspan="8" class="text-center py-8 text-gray-500">
                                <i class="bi bi-cash-stack text-4xl mb-2 block"></i>
                                <p>Henüz işlem bulunmuyor</p>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// Product selection handling
document.querySelector('select[name="product_id"]').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const amountField = document.querySelector('input[name="amount"]');
    amountField.value = '';
});

// Amount formatting
document.querySelector('input[name="amount"]').addEventListener('input', function(e) {
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
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>