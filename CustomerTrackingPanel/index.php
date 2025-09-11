<!-- Bu sistem Oktay ALA tarafından, Analiz Tarım için geliştirilmiştir. -->
<!-- Copyright © Her Hakkı Saklıdır. Ticari amaçlı kullanılması yasaktır. -->

<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
require_once __DIR__ . '/includes/header.php';

$pdo = get_pdo_connection();

$userRole = $_SESSION['user']['rol'] ?? 'user';

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
    // Temel istatistikler
    $totalCustomers = (int) $pdo->query('SELECT COUNT(*) FROM musteriler')->fetchColumn();
    $totalProducts = (int) $pdo->query('SELECT COUNT(*) FROM urunler')->fetchColumn();
    $totalTransactions = (int) $pdo->query('SELECT COUNT(*) FROM islemler')->fetchColumn();
    
    // Toplam Satış: borç + tahsilat işlemlerinin toplamı
    $totalSales = (float) $pdo->query("SELECT COALESCE(SUM(miktar),0) FROM islemler WHERE odeme_tipi IN ('borc','tahsilat')")->fetchColumn();
    
    // Toplam Tahsilat ve Borç (Rapor için kullanılacak)
    $totalCollections = (float) $pdo->query("SELECT COALESCE(SUM(miktar),0) FROM islemler WHERE odeme_tipi = 'tahsilat'")->fetchColumn();
    $totalReceivables = (float) $pdo->query("SELECT COALESCE(SUM(miktar),0) FROM islemler WHERE odeme_tipi = 'borc'")->fetchColumn();
    
    // Tahsilat oranı hesaplama
    $collectionRate = ($totalReceivables > 0) ? ($totalCollections / $totalReceivables) * 100 : 0;
    
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
    
    // En çok işlem yapılan 5 müşteri
    $topCustomersStmt = $pdo->query(
        "SELECT m.isim AS musteri_adi, COUNT(i.id) AS islem_sayisi, SUM(i.miktar) AS toplam_tutar
         FROM islemler i
         JOIN musteriler m ON i.musteri_id = m.id
         GROUP BY i.musteri_id
         ORDER BY islem_sayisi DESC
         LIMIT 5"
    );
    $topCustomers = $topCustomersStmt ? $topCustomersStmt->fetchAll() : [];
    
    // Aylık tahsilat ve borç toplamları (son 6 ay)
    $monthlyStmt = $pdo->query(
        "SELECT DATE_FORMAT(olusturma_zamani, '%Y-%m') AS ay,
            SUM(CASE WHEN odeme_tipi = 'borc' THEN miktar ELSE 0 END) AS toplam_borc,
            SUM(CASE WHEN odeme_tipi = 'tahsilat' THEN miktar ELSE 0 END) AS toplam_tahsilat
         FROM islemler
         WHERE olusturma_zamani >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
         GROUP BY ay
         ORDER BY ay ASC"
    );
    $monthlyData = $monthlyStmt ? $monthlyStmt->fetchAll() : [];
    
    $monthLabels = [];
    $monthlyBorcData = [];
    $monthlyTahsilatData = [];
    $monthMap = [];
    
    foreach ($monthlyData as $row) { $monthMap[$row['ay']] = $row; }
    
    for ($i = 5; $i >= 0; $i--) {
        $monthLabel = date('Y-m', strtotime("-{$i} month"));
        $monthLabels[] = date('M Y', strtotime("-{$i} month")); // Daha okunabilir format
        $monthlyBorcData[] = isset($monthMap[$monthLabel]) ? (float)$monthMap[$monthLabel]['toplam_borc'] : 0.0;
        $monthlyTahsilatData[] = isset($monthMap[$monthLabel]) ? (float)$monthMap[$monthLabel]['toplam_tahsilat'] : 0.0;
    }
}

?>

<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
        <i class="bi bi-speedometer2 mr-2 text-primary-600"></i> Yönetim Paneli
    </h1>

    <?php if ($userRole === 'admin'): ?>
        <!-- Özet İstatistikler -->
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
                    <i class="bi bi-box-seam"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Toplam Ürün</span>
                    <span class="stat-value text-primary-700">
                        <?php echo $totalProducts; ?>
                    </span>
                </div>
            </div>

            <div class="stat-card card-hover animate-slideInUp" style="animation-delay: 0.3s">
                <div class="stat-icon" style="background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);">
                    <i class="bi bi-receipt"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Toplam İşlem</span>
                    <span class="stat-value text-success-700">
                        <?php echo $totalTransactions; ?>
                    </span>
                </div>
            </div>

            <div class="stat-card card-hover animate-slideInUp" style="animation-delay: 0.4s" onclick="showDetailModal('sales')" style="cursor: pointer;">
                <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <i class="bi bi-bag-fill"></i>
                </div>
                <div class="stat-info">
                    <span class="stat-label">Toplam Satış</span>
                    <span class="stat-value text-warning-600">
                        <?php echo number_format($totalSales, 2, ',', '.'); ?> ₺
                    </span>
                </div>
            </div>
        </div>

        <!-- Tahsilat Durumu Özeti -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <div class="card-hover shadow-lg animate-fadeIn">
                <div class="card-header">
                    <h3 class="card-title flex items-center"><i class="bi bi-cash-stack mr-2 text-primary-600"></i> Tahsilat Durumu</h3>
                </div>
                <div class="p-5">
                    <div class="flex flex-col items-center">
                        <div class="relative w-40 h-40 mb-4">
                            <svg class="w-full h-full" viewBox="0 0 36 36">
                                <path class="circle-bg" d="M18 2.0845
                                    a 15.9155 15.9155 0 0 1 0 31.831
                                    a 15.9155 15.9155 0 0 1 0 -31.831"
                                    fill="none" stroke="#eee" stroke-width="3" />
                                <path class="circle" stroke-dasharray="<?php echo min(100, $collectionRate); ?>, 100" d="M18 2.0845
                                    a 15.9155 15.9155 0 0 1 0 31.831
                                    a 15.9155 15.9155 0 0 1 0 -31.831"
                                    fill="none" stroke="#16a34a" stroke-width="3" />
                                <text x="18" y="20.35" class="percentage" text-anchor="middle" alignment-baseline="central" font-size="8">
                                    <?php echo number_format($collectionRate, 1, ',', '.'); ?>%
                                </text>
                            </svg>
                        </div>
                        <div class="grid grid-cols-2 gap-4 w-full">
                            <div class="text-center" onclick="showDetailModal('collections')" style="cursor: pointer;">
                                <p class="text-sm text-gray-500">Toplam Tahsilat</p>
                                <p class="font-bold text-success-600"><?php echo number_format($totalCollections, 2, ',', '.'); ?> ₺</p>
                            </div>
                            <div class="text-center" onclick="showDetailModal('receivables')" style="cursor: pointer;">
                                <p class="text-sm text-gray-500">Toplam Alacak</p>
                                <p class="font-bold text-red-600"><?php echo number_format($totalReceivables, 2, ',', '.'); ?> ₺</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-hover shadow-lg animate-fadeIn lg:col-span-2">
                <div class="card-header">
                    <h3 class="card-title flex items-center"><i class="bi bi-graph-up-arrow mr-2 text-primary-600"></i> Son 14 Gün Borç / Tahsilat</h3>
                </div>
                <div class="p-5">
                    <canvas id="trendChart" height="120"></canvas>
                </div>
            </div>
        </div>

        <!-- Ürün ve Müşteri Analizleri -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="card-hover shadow-lg animate-fadeIn">
                <div class="card-header">
                    <h3 class="card-title flex items-center"><i class="bi bi-pie-chart mr-2 text-primary-600"></i> En Çok Satılan Ürünler</h3>
                </div>
                <div class="p-5">
                    <canvas id="productsChart" height="120"></canvas>
                </div>
            </div>

            <div class="card-hover shadow-lg animate-fadeIn">
                <div class="card-header">
                    <h3 class="card-title flex items-center"><i class="bi bi-people mr-2 text-primary-600"></i> En Aktif Müşteriler</h3>
                </div>
                <div class="p-5">
                    <canvas id="customersChart" height="120"></canvas>
                </div>
            </div>
        </div>

        <!-- Aylık Trend Analizi -->
        <div class="card-hover shadow-lg animate-fadeIn mb-8">
            <div class="card-header">
                <h3 class="card-title flex items-center"><i class="bi bi-bar-chart mr-2 text-primary-600"></i> Son 6 Ay Borç / Tahsilat Trendi</h3>
            </div>
            <div class="p-5">
                <canvas id="monthlyChart" height="100"></canvas>
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
                // Günlük trend verileri
                const dayLabels = <?php echo json_encode($dayLabels ?? []); ?>;
                const borcData = <?php echo json_encode($borcData ?? []); ?>;
                const tahsilatData = <?php echo json_encode($tahsilatData ?? []); ?>;
                
                // Ürün verileri
                const topProducts = <?php echo json_encode($topProducts ?? []); ?>;
                
                // Müşteri verileri
                const topCustomers = <?php echo json_encode($topCustomers ?? []); ?>;
                
                // Aylık trend verileri
                const monthLabels = <?php echo json_encode($monthLabels ?? []); ?>;
                const monthlyBorcData = <?php echo json_encode($monthlyBorcData ?? []); ?>;
                const monthlyTahsilatData = <?php echo json_encode($monthlyTahsilatData ?? []); ?>;

                // Günlük trend grafiği
                const trendCtx = document.getElementById('trendChart');
                if (trendCtx && dayLabels.length) {
                    new Chart(trendCtx, {
                        type: 'line',
                        data: {
                            labels: dayLabels.map(date => {
                                const d = new Date(date);
                                return d.toLocaleDateString('tr-TR', {day: 'numeric', month: 'short'});
                            }),
                            datasets: [
                                { label: 'Borç', data: borcData, borderColor: '#ef4444', backgroundColor: 'rgba(239,68,68,.1)', tension: .3, fill: true },
                                { label: 'Tahsilat', data: tahsilatData, borderColor: '#22c55e', backgroundColor: 'rgba(34,197,94,.1)', tension: .3, fill: true }
                            ]
                        },
                        options: { 
                            responsive: true, 
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return context.dataset.label + ': ' + context.raw.toLocaleString('tr-TR') + ' ₺';
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                // Ürün grafiği
                const productsCtx = document.getElementById('productsChart');
                if (productsCtx && topProducts.length) {
                    new Chart(productsCtx, {
                        type: 'doughnut',
                        data: {
                            labels: topProducts.map(p => p.urun_adi),
                            datasets: [{
                                label: 'Tutar (₺)',
                                data: topProducts.map(p => Number(p.toplam_tutar)),
                                backgroundColor: ['#6366f1','#22c55e','#f59e0b','#ef4444','#06b6d4']
                            }]
                        },
                        options: { 
                            responsive: true, 
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'right',
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return context.label + ': ' + Number(context.raw).toLocaleString('tr-TR') + ' ₺';
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
                
                // Müşteri grafiği
                const customersCtx = document.getElementById('customersChart');
                if (customersCtx && topCustomers.length) {
                    new Chart(customersCtx, {
                        type: 'bar',
                        data: {
                            labels: topCustomers.map(c => c.musteri_adi),
                            datasets: [{
                                label: 'İşlem Sayısı',
                                data: topCustomers.map(c => Number(c.islem_sayisi)),
                                backgroundColor: '#6366f1',
                                order: 1
                            }, {
                                label: 'Toplam Tutar (₺)',
                                data: topCustomers.map(c => Number(c.toplam_tutar)),
                                backgroundColor: '#f59e0b',
                                type: 'line',
                                order: 0,
                                yAxisID: 'y1'
                            }]
                        },
                        options: { 
                            responsive: true, 
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    title: {
                                        display: true,
                                        text: 'İşlem Sayısı'
                                    }
                                },
                                y1: {
                                    beginAtZero: true,
                                    position: 'right',
                                    grid: {
                                        drawOnChartArea: false
                                    },
                                    title: {
                                        display: true,
                                        text: 'Toplam Tutar (₺)'
                                    }
                                }
                            },
                            plugins: {
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            if (context.dataset.label === 'Toplam Tutar (₺)') {
                                                return context.dataset.label + ': ' + context.raw.toLocaleString('tr-TR') + ' ₺';
                                            }
                                            return context.dataset.label + ': ' + context.raw;
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
                
                // Aylık trend grafiği
                const monthlyCtx = document.getElementById('monthlyChart');
                if (monthlyCtx && monthLabels.length) {
                    new Chart(monthlyCtx, {
                        type: 'bar',
                        data: {
                            labels: monthLabels,
                            datasets: [
                                { 
                                    label: 'Borç', 
                                    data: monthlyBorcData, 
                                    backgroundColor: 'rgba(239,68,68,0.7)',
                                    borderColor: '#ef4444',
                                    borderWidth: 1
                                },
                                { 
                                    label: 'Tahsilat', 
                                    data: monthlyTahsilatData, 
                                    backgroundColor: 'rgba(34,197,94,0.7)',
                                    borderColor: '#22c55e',
                                    borderWidth: 1
                                }
                            ]
                        },
                        options: { 
                            responsive: true, 
                            maintainAspectRatio: false,
                            scales: {
                                x: {
                                    grid: {
                                        display: false
                                    }
                                },
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return value.toLocaleString('tr-TR') + ' ₺';
                                        }
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    position: 'top',
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return context.dataset.label + ': ' + context.raw.toLocaleString('tr-TR') + ' ₺';
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
                
                // SVG daire grafiği için stil
                const circlePath = document.querySelector('.circle');
                if (circlePath) {
                    circlePath.style.transformOrigin = 'center';
                    circlePath.style.transform = 'rotate(-90deg)';
                    circlePath.style.transition = 'stroke-dasharray 0.8s ease';
                }
            })();
            </script>
        <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    </div>
</div>

<!-- Detay Modal -->
<div id="detailModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-4xl mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-5 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900" id="modalTitle">Detaylar</h3>
                <button id="closeDetailModal" class="text-gray-400 hover:text-gray-500">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
        </div>
        <div class="p-5">
            <div id="detailContent">
                <!-- İçerik buraya yüklenecek -->
            </div>
        </div>
    </div>
</div>

<script>
function showDetailModal(type) {
    const modal = document.getElementById('detailModal');
    const title = document.getElementById('modalTitle');
    const content = document.getElementById('detailContent');
    
    // Loading göster
    content.innerHTML = '<div class="text-center py-8"><i class="bi bi-hourglass-split text-4xl text-gray-400"></i><p class="mt-2 text-gray-600">Yükleniyor...</p></div>';
    modal.classList.remove('hidden');
    
    // Başlık ayarla
    switch(type) {
        case 'sales':
            title.textContent = 'Toplam Satış Detayları';
            break;
        case 'collections':
            title.textContent = 'Tahsilat Detayları';
            break;
        case 'receivables':
            title.textContent = 'Alacak Detayları';
            break;
    }
    
    // AJAX ile veri çek
    fetch(`detaylar.php?type=${type}`)
        .then(response => response.text())
        .then(data => {
            content.innerHTML = data;
        })
        .catch(error => {
            content.innerHTML = '<div class="text-center py-8 text-red-600"><i class="bi bi-exclamation-triangle text-4xl"></i><p class="mt-2">Veri yüklenirken hata oluştu.</p></div>';
        });
}

// Modal kapatma
document.getElementById('closeDetailModal').addEventListener('click', function() {
    document.getElementById('detailModal').classList.add('hidden');
});

// Modal dışına tıklandığında kapat
document.getElementById('detailModal').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.add('hidden');
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>