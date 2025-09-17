<!-- Bu sistem Oktay ALA tarafından, Analiz Tarım için geliştirilmiştir. -->
<!-- Copyright © Her Hakkı Saklıdır. Ticari amaçlı kullanılması yasaktır. -->

<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
require_once __DIR__ . '/includes/header.php';

$pdo = get_pdo_connection();

// Borçlu müşteriler listesi
$debtorsStmt = $pdo->query("
    SELECT 
        m.id,
        m.isim,
        m.numara,
        COALESCE(SUM(CASE WHEN i.odeme_tipi = 'borc' THEN i.miktar ELSE 0 END), 0) as toplam_borc,
        COALESCE(SUM(CASE WHEN i.odeme_tipi = 'tahsilat' THEN i.miktar ELSE 0 END), 0) as toplam_tahsilat,
        MAX(CASE WHEN i.odeme_tipi = 'borc' THEN i.olusturma_zamani END) as son_borc_tarihi,
        MAX(CASE WHEN i.odeme_tipi = 'tahsilat' THEN i.olusturma_zamani END) as son_odeme_tarihi
    FROM musteriler m
    LEFT JOIN islemler i ON i.musteri_id = m.id
    GROUP BY m.id, m.isim, m.numara
    HAVING (toplam_borc - toplam_tahsilat) > 0
    ORDER BY (toplam_borc - toplam_tahsilat) DESC
");
$debtors = $debtorsStmt->fetchAll();

// Alacaklı müşteriler listesi
$creditorsStmt = $pdo->query("
    SELECT 
        m.id,
        m.isim,
        m.numara,
        COALESCE(SUM(CASE WHEN i.odeme_tipi = 'borc' THEN i.miktar ELSE 0 END), 0) as toplam_borc,
        COALESCE(SUM(CASE WHEN i.odeme_tipi = 'tahsilat' THEN i.miktar ELSE 0 END), 0) as toplam_tahsilat,
        MAX(CASE WHEN i.odeme_tipi = 'tahsilat' THEN i.olusturma_zamani END) as son_odeme_tarihi
    FROM musteriler m
    LEFT JOIN islemler i ON i.musteri_id = m.id
    GROUP BY m.id, m.isim, m.numara
    HAVING (toplam_borc - toplam_tahsilat) < 0
    ORDER BY (toplam_tahsilat - toplam_borc) DESC
");
$creditors = $creditorsStmt->fetchAll();

// Özet istatistikler
$totalDebt = array_sum(array_map(function($d) { return $d['toplam_borc'] - $d['toplam_tahsilat']; }, $debtors));
$totalCredit = abs(array_sum(array_map(function($c) { return $c['toplam_borc'] - $c['toplam_tahsilat']; }, $creditors)));

// Vadesi geçmiş borçlar (30 gün)
$overdueStmt = $pdo->query("
    SELECT 
        m.id,
        m.isim,
        m.numara,
        COALESCE(SUM(CASE WHEN i.odeme_tipi = 'borc' THEN i.miktar ELSE 0 END), 0) as toplam_borc,
        COALESCE(SUM(CASE WHEN i.odeme_tipi = 'tahsilat' THEN i.miktar ELSE 0 END), 0) as toplam_tahsilat,
        MAX(CASE WHEN i.odeme_tipi = 'borc' THEN i.olusturma_zamani END) as son_borc_tarihi,
        DATEDIFF(NOW(), MAX(CASE WHEN i.odeme_tipi = 'borc' THEN i.olusturma_zamani END)) as gun_gecikme
    FROM musteriler m
    LEFT JOIN islemler i ON i.musteri_id = m.id
    GROUP BY m.id, m.isim, m.numara
    HAVING (toplam_borc - toplam_tahsilat) > 0 
        AND gun_gecikme > 30
    ORDER BY gun_gecikme DESC
");
$overdue = $overdueStmt->fetchAll();
?>

<div class="container mx-auto px-4 py-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center">
                <i class="bi bi-credit-card-2-back mr-2 text-primary-600"></i> Ödeme Takip Sistemi
            </h1>
            <p class="text-sm text-gray-600 mt-1">Müşteri borç ve alacak durumlarını takip edin</p>
        </div>
        <div class="flex gap-2">
            <a href="bakiye_dogrula.php" class="btn btn-outline flex items-center">
                <i class="bi bi-shield-check mr-2"></i> Bakiye Doğrula
            </a>
            <button onclick="window.print()" class="btn btn-outline flex items-center">
                <i class="bi bi-printer mr-2"></i> Yazdır
            </button>
        </div>
    </div>

    <!-- Özet İstatistikler -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="stat-card card-hover animate-slideInUp" style="animation-delay: 0.1s">
            <div class="stat-icon" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
                <i class="bi bi-arrow-up-circle"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">Toplam Alacak</span>
                <span class="stat-value text-red-600"><?php echo number_format($totalDebt, 2, ',', '.'); ?> ₺</span>
            </div>
        </div>

        <div class="stat-card card-hover animate-slideInUp" style="animation-delay: 0.2s">
            <div class="stat-icon" style="background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);">
                <i class="bi bi-arrow-down-circle"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">Toplam Borç</span>
                <span class="stat-value text-green-600"><?php echo number_format($totalCredit, 2, ',', '.'); ?> ₺</span>
            </div>
        </div>

        <div class="stat-card card-hover animate-slideInUp" style="animation-delay: 0.3s">
            <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">Vadesi Geçmiş</span>
                <span class="stat-value text-warning-600"><?php echo count($overdue); ?> müşteri</span>
            </div>
        </div>

        <div class="stat-card card-hover animate-slideInUp" style="animation-delay: 0.4s">
            <div class="stat-icon" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);">
                <i class="bi bi-people"></i>
            </div>
            <div class="stat-info">
                <span class="stat-label">Borçlu Müşteri</span>
                <span class="stat-value text-primary-600"><?php echo count($debtors); ?> kişi</span>
            </div>
        </div>
    </div>

    <!-- Vadesi Geçmiş Borçlar -->
    <?php if (!empty($overdue)): ?>
    <div class="card-hover animate-fadeIn mb-6 shadow-lg border-l-4 border-red-500">
        <div class="card-header bg-red-50">
            <h3 class="card-title flex items-center text-red-700">
                <i class="bi bi-exclamation-triangle-fill mr-2"></i>
                Vadesi Geçmiş Borçlar (<?php echo count($overdue); ?> müşteri)
            </h3>
        </div>
        <div class="p-0">
            <div class="table-container">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Müşteri</th>
                            <th>Telefon</th>
                            <th>Borç Tutarı</th>
                            <th>Son Borç Tarihi</th>
                            <th>Gecikme</th>
                            <th class="text-right">İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($overdue as $customer): ?>
                            <?php $debt = $customer['toplam_borc'] - $customer['toplam_tahsilat']; ?>
                            <tr class="hover:bg-red-50">
                                <td>
                                    <a href="musteri_rapor.php?customer=<?php echo $customer['id']; ?>" class="font-medium text-red-700 hover:text-red-900">
                                        <?php echo htmlspecialchars($customer['isim']); ?>
                                    </a>
                                </td>
                                <td><?php echo htmlspecialchars($customer['numara']); ?></td>
                                <td class="font-medium text-red-600">
                                    +<?php echo number_format($debt, 2, ',', '.'); ?> ₺
                                </td>
                                <td><?php echo date('d.m.Y', strtotime($customer['son_borc_tarihi'])); ?></td>
                                <td>
                                    <span class="badge bg-red-100 text-red-800">
                                        <?php echo $customer['gun_gecikme']; ?> gün
                                    </span>
                                </td>
                                <td class="text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="islemler.php?customer=<?php echo $customer['id']; ?>" class="btn btn-outline btn-sm text-primary" title="Tahsilat Ekle">
                                            <i class="bi bi-cash-coin"></i>
                                        </a>
                                        <a href="tel:<?php echo $customer['numara']; ?>" class="btn btn-outline btn-sm text-success" title="Ara">
                                            <i class="bi bi-telephone"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Borçlu ve Alacaklı Müşteriler -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Borçlu Müşteriler -->
        <div class="card-hover animate-fadeIn shadow-lg">
            <div class="card-header">
                <h3 class="card-title flex items-center text-red-700">
                    <i class="bi bi-arrow-up-circle-fill mr-2"></i>
                    Borçlu Müşteriler (<?php echo count($debtors); ?>)
                </h3>
            </div>
            <div class="p-0">
                <?php if (empty($debtors)): ?>
                    <div class="p-8 text-center text-gray-500">
                        <i class="bi bi-check-circle text-4xl text-green-500 mb-3 block"></i>
                        <p>Harika! Borçlu müşteri bulunmuyor.</p>
                    </div>
                <?php else: ?>
                    <div class="table-container max-h-96 overflow-y-auto">
                        <table class="table table-hover">
                            <thead class="sticky top-0 bg-white">
                                <tr>
                                    <th>Müşteri</th>
                                    <th>Borç</th>
                                    <th>Son İşlem</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($debtors as $customer): ?>
                                    <?php 
                                    $debt = $customer['toplam_borc'] - $customer['toplam_tahsilat'];
                                    $lastDate = $customer['son_odeme_tarihi'] ?: $customer['son_borc_tarihi'];
                                    $daysSince = $lastDate ? floor((time() - strtotime($lastDate)) / 86400) : null;
                                    ?>
                                    <tr class="hover:bg-red-50">
                                        <td>
                                            <div>
                                                <a href="musteri_rapor.php?customer=<?php echo $customer['id']; ?>" class="font-medium text-red-700 hover:text-red-900">
                                                    <?php echo htmlspecialchars($customer['isim']); ?>
                                                </a>
                                                <?php if ($customer['numara']): ?>
                                                    <div class="text-xs text-gray-500"><?php echo htmlspecialchars($customer['numara']); ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="font-medium text-red-600">
                                            +<?php echo number_format($debt, 2, ',', '.'); ?> ₺
                                        </td>
                                        <td class="text-sm text-gray-600">
                                            <?php if ($lastDate): ?>
                                                <?php echo date('d.m.Y', strtotime($lastDate)); ?>
                                                <?php if ($daysSince !== null): ?>
                                                    <div class="text-xs <?php echo $daysSince > 30 ? 'text-red-500' : 'text-gray-400'; ?>">
                                                        <?php echo $daysSince; ?> gün önce
                                                    </div>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-gray-400">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-right">
                                            <a href="islemler.php?customer=<?php echo $customer['id']; ?>" class="btn btn-outline btn-sm text-primary" title="Tahsilat Ekle">
                                                <i class="bi bi-plus-circle"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Alacaklı Müşteriler -->
        <div class="card-hover animate-fadeIn shadow-lg">
            <div class="card-header">
                <h3 class="card-title flex items-center text-green-700">
                    <i class="bi bi-arrow-down-circle-fill mr-2"></i>
                    Alacaklı Müşteriler (<?php echo count($creditors); ?>)
                </h3>
            </div>
            <div class="p-0">
                <?php if (empty($creditors)): ?>
                    <div class="p-8 text-center text-gray-500">
                        <i class="bi bi-dash-circle text-4xl text-gray-400 mb-3 block"></i>
                        <p>Alacaklı müşteri bulunmuyor.</p>
                    </div>
                <?php else: ?>
                    <div class="table-container max-h-96 overflow-y-auto">
                        <table class="table table-hover">
                            <thead class="sticky top-0 bg-white">
                                <tr>
                                    <th>Müşteri</th>
                                    <th>Alacak</th>
                                    <th>Son Ödeme</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($creditors as $customer): ?>
                                    <?php 
                                    $credit = abs($customer['toplam_borc'] - $customer['toplam_tahsilat']);
                                    $daysSince = $customer['son_odeme_tarihi'] ? floor((time() - strtotime($customer['son_odeme_tarihi'])) / 86400) : null;
                                    ?>
                                    <tr class="hover:bg-green-50">
                                        <td>
                                            <div>
                                                <a href="musteri_rapor.php?customer=<?php echo $customer['id']; ?>" class="font-medium text-green-700 hover:text-green-900">
                                                    <?php echo htmlspecialchars($customer['isim']); ?>
                                                </a>
                                                <?php if ($customer['numara']): ?>
                                                    <div class="text-xs text-gray-500"><?php echo htmlspecialchars($customer['numara']); ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="font-medium text-green-600">
                                            <?php echo number_format($credit, 2, ',', '.'); ?> ₺
                                        </td>
                                        <td class="text-sm text-gray-600">
                                            <?php if ($customer['son_odeme_tarihi']): ?>
                                                <?php echo date('d.m.Y', strtotime($customer['son_odeme_tarihi'])); ?>
                                                <?php if ($daysSince !== null): ?>
                                                    <div class="text-xs text-gray-400"><?php echo $daysSince; ?> gün önce</div>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-gray-400">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-right">
                                            <a href="islemler.php?customer=<?php echo $customer['id']; ?>" class="btn btn-outline btn-sm text-primary" title="İşlem Ekle">
                                                <i class="bi bi-plus-circle"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .btn, .card-header, nav, header, footer {
        display: none !important;
    }
    
    .card-hover {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
    }
    
    .container {
        max-width: none !important;
        padding: 0 !important;
    }
    
    .grid {
        display: block !important;
    }
    
    .card-hover {
        break-inside: avoid;
        margin-bottom: 20px;
    }
}
</style>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
