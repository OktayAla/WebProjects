<?php 
require_once __DIR__ . '/includes/auth.php'; 
require_login(); 
require_once __DIR__ . '/includes/header.php'; 

$pdo = get_pdo_connection();
$customerId = isset($_GET['customer']) ? (int)$_GET['customer'] : 0;
$customer = null;
if ($customerId) {
    $stmt = $pdo->prepare('SELECT * FROM musteriler WHERE id = ?');
    $stmt->execute([$customerId]);
    $customer = $stmt->fetch();
}
if (!$customer) {
    echo '<div class="alert alert-danger">Müşteri bulunamadı.</div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

// Toplam Borç (Satış)
$salesStmt = $pdo->prepare("SELECT COALESCE(SUM(miktar), 0) FROM islemler WHERE musteri_id = ? AND odeme_tipi = 'borc'");
$salesStmt->execute([$customerId]);
$totalSales = (float)$salesStmt->fetchColumn();

// Toplam Tahsilat (Ödeme)
$paidStmt = $pdo->prepare("SELECT COALESCE(SUM(miktar), 0) FROM islemler WHERE musteri_id = ? AND odeme_tipi = 'tahsilat'");
$paidStmt->execute([$customerId]);
$totalPaid = (float)$paidStmt->fetchColumn();

// Net Bakiye (Tahsilat - Borç) -> Pozitif: alacaklı, Negatif: borçlu
$remaining = $totalPaid - $totalSales;

$historyStmt = $pdo->prepare('SELECT i.id, i.odeme_tipi, i.miktar, i.aciklama, i.olusturma_zamani, u.isim AS urun_isim FROM islemler i LEFT JOIN urunler u ON u.id = i.urun_id WHERE i.musteri_id = ? ORDER BY i.olusturma_zamani DESC');
$historyStmt->execute([$customerId]);
$history = $historyStmt->fetchAll();
?>

<div class="floating-element"></div>
<div class="floating-element"></div>
<div class="floating-element"></div>


<div class="container mx-auto px-4 py-6">
    <div class="flex items-center space-x-2 text-sm text-gray-500 mb-6">
        <a href="index.php" class="hover:text-primary-600 transition-colors duration-200">Panel</a>
        <span class="text-gray-400">/</span>
        <a href="musteriler.php" class="hover:text-primary-600 transition-colors duration-200">Müşteriler</a>
        <span class="text-gray-400">/</span>
        <span class="text-gray-700 font-medium"><?php echo htmlspecialchars($customer['isim']); ?></span>
    </div>

    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">Müşteri Detayları</h2>
            <p class="text-sm text-gray-600 mt-1">Müşteri bilgileri ve işlem geçmişi</p>
        </div>
        <div class="flex space-x-2">
            <a href="islemler.php?customer=<?php echo $customerId; ?>" class="btn btn-outline flex items-center">
                <i class="bi bi-arrow-left mr-2"></i> İşlemlere Dön
            </a>
            <button onclick="window.print()" class="btn btn-outline flex items-center">
                <i class="bi bi-printer mr-2"></i> Yazdır
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-12 gap-6 mb-6">
        <div class="md:col-span-5">
            <div class="card-hover animate-fadeIn">
                <div class="card-header">
                    <h3 class="card-title">Müşteri Bilgileri</h3>
                </div>
                <div class="p-4">
                    <div class="space-y-4">
                        <div class="flex flex-col">
                            <span class="text-sm font-medium text-gray-500">Ad Soyad</span>
                            <span class="text-base font-semibold"><?php echo htmlspecialchars($customer['isim']); ?></span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm font-medium text-gray-500">Telefon</span>
                            <span class="text-base"><?php echo htmlspecialchars($customer['numara']); ?></span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm font-medium text-gray-500">Adres</span>
                            <span class="text-base whitespace-pre-line"><?php echo htmlspecialchars($customer['adres']); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
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
                
                <div class="stat-card card-hover" style="animation-delay: 0.3s">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                    <div class="stat-info">
                        <span class="stat-label">Toplam Tahsilat</span>
                        <span class="stat-value"><?php echo number_format($totalPaid, 2, ',', '.'); ?> ₺</span>
                    </div>
                </div>
                
                <div class="stat-card card-hover animate-fadeIn" style="animation-delay: 0.3s">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <div class="stat-info">
                        <span class="stat-label">Net Bakiye</span>
                        <span class="stat-value <?php echo $remaining < 0 ? 'text-danger-600' : ($remaining > 0 ? 'text-success-600' : ''); ?>">
                            <?php echo ($remaining > 0 ? '+' : '') . number_format($remaining, 2, ',', '.'); ?> ₺
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                            <th>Ürün</th>
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
                                <td><?php echo date('d.m.Y H:i', strtotime($row['olusturma_zamani'])); ?></td>
                                <td>
                                    <?php if ($row['odeme_tipi'] === 'borc'): ?>
                                    <span class="badge-debit">Borç</span>
                                    <?php else: ?>
                                    <span class="badge-credit">Tahsilat</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($row['urun_isim']): ?>
                                        <span class="badge badge-outline"><?php echo htmlspecialchars($row['urun_isim']); ?></span>
                                    <?php else: ?>
                                        <span class="text-gray-400">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="font-medium <?php echo $row['odeme_tipi'] === 'borc' ? 'text-danger-600' : 'text-success-600'; ?>">
                                    <?php 
                                        $signedAmount = ($row['odeme_tipi'] === 'borc' ? '-' : '+') . number_format($row['miktar'], 2, ',', '.');
                                        echo $signedAmount;
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($row['aciklama']); ?></td>
                                <td class="text-right">
                                    <a href="print.php?id=<?php echo $row['id']; ?>" class="btn btn-outline btn-sm">
                                        <i class="bi bi-printer mr-1"></i> Yazdır
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-8 text-gray-500">
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