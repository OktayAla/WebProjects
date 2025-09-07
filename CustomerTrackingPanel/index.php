<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
require_once __DIR__ . '/includes/header.php';

$pdo = get_pdo_connection();

$userRole = $_SESSION['user']['rol'] ?? 'user';

// Son yapılan işlemler (10 adet) - tüm roller için
$recentStmtGlobal = $pdo->query(
	"SELECT i.id, i.miktar, i.odeme_tipi, i.aciklama, i.olusturma_zamani,
		m.isim AS musteri_isim, COALESCE(u.isim,'-') AS urun_isim
	 FROM islemler i
	 JOIN musteriler m ON m.id = i.musteri_id
	 LEFT JOIN urunler u ON u.id = i.urun_id
	 ORDER BY i.olusturma_zamani DESC
	 LIMIT 10"
);
$recentTransactions = $recentStmtGlobal ? $recentStmtGlobal->fetchAll() : [];

if ($userRole === 'admin') {
    $totalCustomers = (int) $pdo->query('SELECT COUNT(*) FROM musteriler')->fetchColumn();
    
    // Toplam Satış: borç + tahsilat işlemlerinin toplamı
    $totalSales = (float) $pdo->query("SELECT COALESCE(SUM(miktar),0) FROM islemler WHERE odeme_tipi IN ('borc','tahsilat')")->fetchColumn();
    
    // Toplam Tahsilat: sadece tahsilat işlemleri
    $totalCollections = (float) $pdo->query("SELECT COALESCE(SUM(miktar),0) FROM islemler WHERE odeme_tipi = 'tahsilat'")->fetchColumn();
    
    // Toplam Alacak: sadece borç işlemleri (işlemler tablosundan)
    $totalReceivables = (float) $pdo->query("SELECT COALESCE(SUM(miktar),0) FROM islemler WHERE odeme_tipi = 'borc'")->fetchColumn();

	// Günlük trend (son 14 gün)
	$trendStmt = $pdo->query(
		"SELECT DATE(olusturma_zamani) AS gun,
			SUM(CASE WHEN odeme_tipi = 'borc' THEN miktar ELSE 0 END) AS toplam_borc,
			SUM(CASE WHEN odeme_tipi = 'tahsilat' THEN miktar ELSE 0 END) AS toplam_tahsilat
		 FROM islemler
		 WHERE olusturma_zamani >= DATE_SUB(CURDATE(), INTERVAL 14 DAY)
		 GROUP BY DATE(olusturma_zamani)
		 ORDER BY gun ASC"
	);
	$rawTrend = $trendStmt ? $trendStmt->fetchAll() : [];

	$dayLabels = [];
	$borcData = [];
	$tahsilatData = [];
	$map = [];
	foreach ($rawTrend as $row) { $map[$row['gun']] = $row; }
	for ($i = 13; $i >= 0; $i--) {
		$label = date('Y-m-d', strtotime("-{$i} day"));
		$dayLabels[] = $label;
		$borcData[] = isset($map[$label]) ? (float)$map[$label]['toplam_borc'] : 0.0;
		$tahsilatData[] = isset($map[$label]) ? (float)$map[$label]['toplam_tahsilat'] : 0.0;
	}

	// En çok satılan (borçlanan) 5 ürün
	$topProductsStmt = $pdo->query(
		"SELECT COALESCE(u.isim, 'Diğer') AS urun_adi, SUM(i.miktar) AS toplam_tutar
		 FROM islemler i
		 LEFT JOIN urunler u ON u.id = i.urun_id
		 WHERE i.odeme_tipi = 'borc' AND i.urun_id IS NOT NULL
		 GROUP BY urun_adi
		 ORDER BY toplam_tutar DESC
		 LIMIT 5"
	);
	$topProducts = $topProductsStmt ? $topProductsStmt->fetchAll() : [];

}

?>

<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
        <i class="bi bi-speedometer2 mr-2 text-primary-600"></i> Yönetim Paneli
    </h1>

    <?php if ($userRole === 'admin'): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 dashboard-stats">
            <div class="stat-card card-hover animate-slideInUp" style="animation-delay: 0.1s">
                <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
                <div class="stat-info">
                    <span class="stat-label">Toplam Müşteri</span>
                    <span class="stat-value"><?php echo $totalCustomers; ?></span>
                </div>
            </div>

            <div class="stat-card card-hover animate-slideInUp" style="animation-delay: 0.2s">
                <div class="stat-icon" style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);">
                    <i class="bi bi-bag-fill"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Toplam Satış</span>
                    <span class="stat-value text-primary-700">
                        <?php echo number_format($totalSales, 2, ',', '.'); ?> ₺
                    </span>
                </div>
            </div>

            <div class="stat-card card-hover animate-slideInUp" style="animation-delay: 0.3s">
                <div class="stat-icon" style="background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Toplam Tahsilat</span>
                    <span class="stat-value text-success-700">
                        <?php echo number_format($totalCollections, 2, ',', '.'); ?> ₺
                    </span>
                </div>
            </div>

            <div class="stat-card card-hover animate-slideInUp" style="animation-delay: 0.4s">
                <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <i class="bi bi-wallet2"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Toplam Alacak</span>
                    <span class="stat-value text-red-600" style="color: red !important;">
                        <?php echo number_format($totalReceivables, 2, ',', '.'); ?> ₺
                    </span>
                </div>
            </div>
        </div>

        <!-- Admin Grafikler + Son İşlemler -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="card-hover shadow-lg animate-fadeIn lg:col-span-2">
                <div class="card-header">
                    <h3 class="card-title flex items-center"><i class="bi bi-graph-up-arrow mr-2 text-primary-600"></i> Son 14 Gün Borç / Tahsilat</h3>
                </div>
                <div class="p-5">
                    <canvas id="trendChart" height="120"></canvas>
                </div>
            </div>

            <div class="card-hover shadow-lg animate-fadeIn" style="animation-delay: .1s">
                <div class="card-header">
                    <h3 class="card-title flex items-center"><i class="bi bi-pie-chart mr-2 text-primary-600"></i> En Çok Satılan Ürünler</h3>
                </div>
                <div class="p-5">
                    <canvas id="productsChart" height="120"></canvas>
                </div>
            </div>
        </div>

        <?php endif; ?>

        <div class="card-hover shadow-lg animate-fadeIn mb-8">
            <div class="card-header">
                <h3 class="card-title flex items-center"><i class="bi bi-clock-history mr-2 text-primary-600"></i> Son Yapılan İşlemler</h3>
            </div>
            <div class="p-0">
                <div class="table-container">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Müşteri</th>
                                <th>Ürün</th>
                                <th>Tarih</th>
                                <th>Tutar (₺)</th>
                                <th>Tür</th>
                                <th>Açıklama</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($recentTransactions)): ?>
                                <?php foreach ($recentTransactions as $row): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['musteri_isim']); ?></td>
                                        <td><?php echo $row['urun_isim'] ? '<span class="badge badge-outline">'.htmlspecialchars($row['urun_isim']).'</span>' : '<span class="text-gray-400">-</span>'; ?></td>
                                        <td><?php echo date('d.m.Y H:i', strtotime($row['olusturma_zamani'])); ?></td>
                                        <td class="font-medium"><?php echo number_format($row['miktar'], 2, ',', '.'); ?> ₺</td>
                                        <td>
                                            <?php if ($row['odeme_tipi'] === 'borc'): ?>
                                                <span class="badge-debit w-fit inline-flex items-center"><i class="bi bi-arrow-down-right mr-1"></i>Borç</span>
                                            <?php else: ?>
                                                <span class="badge-credit w-fit inline-flex items-center"><i class="bi bi-arrow-up-right mr-1"></i>Tahsilat</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $row['aciklama'] ? htmlspecialchars($row['aciklama']) : '<span class="text-gray-400 italic">-</span>'; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-6 text-gray-500">Kayıt bulunamadı.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <?php if ($userRole === 'admin'): ?>
            <!-- Chart.js ve veriler (sadece admin) -->
            <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
            <script>
            (function(){
                const dayLabels = <?php echo json_encode($dayLabels ?? []); ?>;
                const borcData = <?php echo json_encode($borcData ?? []); ?>;
                const tahsilatData = <?php echo json_encode($tahsilatData ?? []); ?>;
                const topProducts = <?php echo json_encode($topProducts ?? []); ?>;

                const trendCtx = document.getElementById('trendChart');
                if (trendCtx && dayLabels.length) {
                    new Chart(trendCtx, {
                        type: 'line',
                        data: {
                            labels: dayLabels,
                            datasets: [
                                { label: 'Borç', data: borcData, borderColor: '#ef4444', backgroundColor: 'rgba(239,68,68,.1)', tension: .3, fill: true },
                                { label: 'Tahsilat', data: tahsilatData, borderColor: '#22c55e', backgroundColor: 'rgba(34,197,94,.1)', tension: .3, fill: true }
                            ]
                        },
                        options: { responsive: true, maintainAspectRatio: false }
                    });
                }

                const productsCtx = document.getElementById('productsChart');
                if (productsCtx && topProducts.length) {
                    new Chart(productsCtx, {
                        type: 'bar',
                        data: {
                            labels: topProducts.map(p => p.urun_adi),
                            datasets: [{
                                label: 'Tutar (₺)',
                                data: topProducts.map(p => Number(p.toplam_tutar)),
                                backgroundColor: ['#6366f1','#22c55e','#f59e0b','#ef4444','#06b6d4']
                            }]
                        },
                        options: { responsive: true, maintainAspectRatio: false }
                    });
                }
            })();
            </script>
        <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>