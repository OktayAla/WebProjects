<?php 
require_once __DIR__ . '/includes/auth.php'; 
require_login(); 
require_once __DIR__ . '/includes/header.php'; 

$pdo = get_pdo_connection();
$customerId = isset($_GET['customer']) ? (int)$_GET['customer'] : 0;
$customer = null;
if ($customerId) {
    $stmt = $pdo->prepare('SELECT * FROM customers WHERE id = ?');
    $stmt->execute([$customerId]);
    $customer = $stmt->fetch();
}
if (!$customer) {
    echo '<div class="alert alert-danger">Müşteri bulunamadı.</div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$totalSales = (float)$pdo->prepare("SELECT COALESCE(SUM(amount),0) FROM transactions WHERE customer_id = ? AND type='debit'")->execute([$customerId]) ? $pdo->query("SELECT COALESCE(SUM(amount),0) FROM transactions WHERE customer_id = $customerId AND type='debit'")->fetchColumn() : 0.0;
$totalPaid  = (float)$pdo->prepare("SELECT COALESCE(SUM(amount),0) FROM transactions WHERE customer_id = ? AND type='credit'")->execute([$customerId]) ? $pdo->query("SELECT COALESCE(SUM(amount),0) FROM transactions WHERE customer_id = $customerId AND type='credit'")->fetchColumn() : 0.0;
$remaining  = (float)$customer['balance'];

$historyStmt = $pdo->prepare('SELECT id, type, amount, note, created_at FROM transactions WHERE customer_id = ? ORDER BY created_at DESC');
$historyStmt->execute([$customerId]);
$history = $historyStmt->fetchAll();
?>

<!-- Floating background elements -->
<div class="floating-element"></div>
<div class="floating-element"></div>
<div class="floating-element"></div>

<div class="dashboard-header">
    <div class="container mx-auto px-4">
        <h1 class="dashboard-title"><?php echo htmlspecialchars($customer['name']); ?> - Müşteri Raporu</h1>
        <p class="dashboard-subtitle">Müşteri detayları ve işlem geçmişi</p>
    </div>
</div>

<div class="container mx-auto px-4 py-6">
    <!-- Breadcrumb -->
    <div class="flex items-center space-x-2 text-sm text-gray-500 mb-6">
        <a href="index.php" class="hover:text-primary-600 transition-colors duration-200">Panel</a>
        <span class="text-gray-400">/</span>
        <a href="customers.php" class="hover:text-primary-600 transition-colors duration-200">Müşteriler</a>
        <span class="text-gray-400">/</span>
        <span class="text-gray-700 font-medium"><?php echo htmlspecialchars($customer['name']); ?></span>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">Müşteri Detayları</h2>
            <p class="text-sm text-gray-600 mt-1">Müşteri bilgileri ve işlem geçmişi</p>
        </div>
        <div class="flex space-x-2">
            <a href="transactions.php?customer=<?php echo $customerId; ?>" class="btn btn-outline flex items-center">
                <i class="bi bi-arrow-left mr-2"></i> İşlemlere Dön
            </a>
            <button onclick="window.print()" class="btn btn-outline flex items-center">
                <i class="bi bi-printer mr-2"></i> Yazdır
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 mb-6">
        <!-- Customer Info -->
        <div class="md:col-span-5">
            <div class="card-hover animate-fadeIn">
                <div class="card-header">
                    <h3 class="card-title">Müşteri Bilgileri</h3>
                </div>
                <div class="p-4">
                    <div class="space-y-4">
                        <div class="flex flex-col">
                            <span class="text-sm font-medium text-gray-500">Ad Soyad</span>
                            <span class="text-base font-semibold"><?php echo htmlspecialchars($customer['name']); ?></span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm font-medium text-gray-500">Telefon</span>
                            <span class="text-base"><?php echo htmlspecialchars($customer['phone']); ?></span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm font-medium text-gray-500">Adres</span>
                            <span class="text-base whitespace-pre-line"><?php echo htmlspecialchars($customer['address']); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Stats -->
        <div class="md:col-span-7">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="stat-card card-hover animate-fadeIn" style="animation-delay: 0.1s">
                    <div class="stat-icon">
                        <i class="bi bi-cart"></i>
                    </div>
                    <div class="stat-info">
                        <span class="stat-label">Toplam Satış</span>
                        <span class="stat-value"><?php echo number_format($totalSales, 2, ',', '.'); ?> ₺</span>
                    </div>
                </div>
                
                <div class="stat-card card-hover animate-fadeIn" style="animation-delay: 0.2s">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);">
                        <i class="bi bi-cash"></i>
                    </div>
                    <div class="stat-info">
                        <span class="stat-label">Ödenen Toplam</span>
                        <span class="stat-value"><?php echo number_format($totalPaid, 2, ',', '.'); ?> ₺</span>
                    </div>
                </div>
                
                <div class="stat-card card-hover animate-fadeIn" style="animation-delay: 0.3s">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <div class="stat-info">
                        <span class="stat-label">Kalan Borç</span>
                        <span class="stat-value"><?php echo number_format($remaining, 2, ',', '.'); ?> ₺</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction History -->
    <div class="card-hover animate-fadeIn" style="animation-delay: 0.4s">
        <div class="card-header">
            <div class="flex justify-between items-center">
                <h3 class="card-title">İşlem Geçmişi (<?php echo count($history); ?> adet)</h3>
            </div>
        </div>
        <div class="p-0">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tarih</th>
                            <th>Tür</th>
                            <th>Tutar (₺)</th>
                            <th>Not</th>
                            <th class="text-right">İşlem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($history) > 0): ?>
                            <?php foreach ($history as $index => $row): ?>
                            <tr class="animate-fadeIn" style="animation-delay: <?php echo 0.5 + ($index * 0.05); ?>s">
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo date('d.m.Y H:i', strtotime($row['created_at'])); ?></td>
                                <td>
                                    <?php if ($row['type'] === 'debit'): ?>
                                    <span class="badge-debit">Borç</span>
                                    <?php else: ?>
                                    <span class="badge-credit">Tahsilat</span>
                                    <?php endif; ?>
                                </td>
                                <td class="font-medium"><?php echo number_format($row['amount'], 2, ',', '.'); ?></td>
                                <td><?php echo htmlspecialchars($row['note']); ?></td>
                                <td class="text-right">
                                    <a href="print.php?id=<?php echo $row['id']; ?>" class="btn btn-outline btn-sm">
                                        <i class="bi bi-printer mr-1"></i> Yazdır
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-8 text-gray-500">
                                <i class="bi bi-inbox text-3xl mb-2 block"></i>
                                Henüz işlem kaydı bulunmuyor
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>