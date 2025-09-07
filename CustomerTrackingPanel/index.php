<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
require_once __DIR__ . '/includes/header.php';

$pdo = get_pdo_connection();

$userRole = $_SESSION['user']['rol'] ?? 'user';

if ($userRole === 'admin') {
    $totalCustomers = (int) $pdo->query('SELECT COUNT(*) FROM musteriler')->fetchColumn();
    
    // Toplam Satış: borç + tahsilat işlemlerinin toplamı
    $totalSales = (float) $pdo->query("SELECT COALESCE(SUM(miktar),0) FROM islemler WHERE odeme_tipi IN ('borc','tahsilat')")->fetchColumn();
    
    // Toplam Tahsilat: sadece tahsilat işlemleri
    $totalCollections = (float) $pdo->query("SELECT COALESCE(SUM(miktar),0) FROM islemler WHERE odeme_tipi = 'tahsilat'")->fetchColumn();
    
    // Toplam Alacak: sadece borç işlemleri (işlemler tablosundan)
    $totalReceivables = (float) $pdo->query("SELECT COALESCE(SUM(miktar),0) FROM islemler WHERE odeme_tipi = 'borc'")->fetchColumn();
}

$borcluSayfa = isset($_GET['borclu_sayfa']) ? (int) $_GET['borclu_sayfa'] : 1;
$borcluSayfaBasina = 7;
$satisSayfa = isset($_GET['satis_sayfa']) ? (int) $_GET['satis_sayfa'] : 1;
$satisSayfaBasina = 6;
$borcluOffset = ($borcluSayfa - 1) * $borcluSayfaBasina;
$satisOffset = ($satisSayfa - 1) * $satisSayfaBasina;
$toplamBorcluMusteri = (int) $pdo->query('SELECT COUNT(*) FROM musteriler WHERE tutar > 0')->fetchColumn();
$toplamSatisKaydi = (int) $pdo->query("SELECT COUNT(*) FROM islemler WHERE odeme_tipi IN ('borc', 'tahsilat')")->fetchColumn();

?>

<div class="floating-element"></div>
<div class="floating-element"></div>
<div class="floating-element"></div>

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
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="card-hover animate-fadeIn" style="animation-delay: 0.5s">
                <div class="card-header">
                    <div class="flex justify-between items-center">
                        <h3 class="card-title"><i class="bi bi-receipt mr-2"></i>Son Satışlar</h3>
                        <a href="islemler.php" class="btn btn-outline btn-sm">
                            Tümünü Gör <i class="bi bi-chevron-right ml-1"></i>
                        </a>
                    </div>
                </div>
                <div class="p-0">
                    <div class="overflow-x-auto">
                        <table class="table table-hover table-stacked w-full">
                            <thead>
                                <tr>
                                    <th>Müşteri</th>
                                    <th>Tarih</th>
                                    <th>Tutar</th>
                                    <th>Not</th>
                                    <th class="text-center">İşlem</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sonSatislarSorgusu = "SELECT i.id, i.musteri_id, i.odeme_tipi, m.isim AS musteri_isim, i.miktar, i.aciklama, i.olusturma_zamani 
                                                          FROM islemler i 
                                                          JOIN musteriler m ON m.id = i.musteri_id 
                                                          WHERE i.odeme_tipi IN ('borc', 'tahsilat')
                                                          ORDER BY i.olusturma_zamani DESC 
                                                          LIMIT $satisSayfaBasina OFFSET $satisOffset";
                                $sonSatislar = $pdo->query($sonSatislarSorgusu);
                                $i = 0;
                                foreach ($sonSatislar as $row):
                                    $i++;
                                    $isBorc = $row['odeme_tipi'] === 'borc';
                                    ?>
                                    <tr class="animate-fadeIn" style="animation-delay: <?php echo 0.5 + ($i * 0.05); ?>s">
                                        <td data-label="Müşteri">
                                            <a href="musteri_rapor.php?customer=<?php echo (int) $row['musteri_id']; ?>"
                                                class="text-primary-600 hover:text-primary-900 transition-colors duration-200">
                                                <?php echo htmlspecialchars($row['musteri_isim']); ?>
                                            </a>
                                        </td>
                                        <td data-label="Tarih">
                                            <span class="text-gray-600"><i
                                                    class="bi bi-calendar3 mr-1"></i><?php echo date('d.m.Y H:i', strtotime($row['olusturma_zamani'])); ?></span>
                                        </td>
                                        <td data-label="Tutar"
                                            class="font-medium <?php echo $isBorc ? 'text-primary-700' : 'text-success-600'; ?>">
                                            <?php echo number_format($row['miktar'], 2, ',', '.'); ?> ₺
                                        </td>
                                        <td data-label="Not"><?php echo htmlspecialchars($row['aciklama']); ?>
                                        </td>
                                        <td class="text-center" data-label="İşlem">
                                            <span class="badge <?php echo $isBorc ? 'badge-primary' : 'badge-success'; ?>">
                                                <i
                                                    class="bi <?php echo $isBorc ? 'bi-arrow-up-circle' : 'bi-arrow-down-circle'; ?> mr-1"></i>
                                                <?php echo $isBorc ? 'Borç' : 'Tahsilat'; ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if ($i === 0): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-8 text-gray-500">
                                            <i class="bi bi-inbox text-3xl mb-2 block"></i>
                                            Henüz satış kaydı bulunmuyor.
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <?php
                    function sayfalamaLinkleri($toplamKayitSayisi, $sayfaBasinaKayitSayisi, $mevcutSayfa, $sayfaParametresi)
                    {
                        $toplamSayfaSayisi = ceil($toplamKayitSayisi / $sayfaBasinaKayitSayisi);
                        if ($toplamSayfaSayisi > 1) {
                            echo '<div class="join">';
                            for ($i = 1; $i <= $toplamSayfaSayisi; $i++) {
                                $aktifClass = ($i == $mevcutSayfa) ? 'join-item btn btn-active' : 'join-item btn';
                                echo "<a href='?{$sayfaParametresi}={$i}' class='{$aktifClass}'>{$i}</a>";
                            }
                            echo '</div>';
                        }
                    }
                    sayfalamaLinkleri($toplamSatisKaydi, $satisSayfaBasina, $satisSayfa, 'satis_sayfa');
                    ?>
                </div>
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="card-hover animate-fadeIn" style="animation-delay: 0.6s">
                <div class="card-header">
                    <div class="flex justify-between items-center">
                        <h3 class="card-title"><i class="bi bi-exclamation-triangle mr-2"></i>Borçlu Müşteriler</h3>
                        <a href="musteriler.php" class="btn btn-outline btn-sm">
                            Tümünü Gör <i class="bi bi-chevron-right ml-1"></i>
                        </a>
                    </div>
                </div>
                <div class="p-4">
                    <div class="space-y-3 max-w-full">
                        <?php
                        $borcluMusterilerSorgusu = "SELECT id, isim, tutar 
                                                        FROM musteriler 
                                                        WHERE tutar > 0 
                                                        ORDER BY tutar DESC 
                                                        LIMIT $borcluSayfaBasina OFFSET $borcluOffset";
                        $borcluMusteriler = $pdo->query($borcluMusterilerSorgusu);
                        $hasDebtors = false;
                        $i = 0;
                        foreach ($borcluMusteriler as $debtor):
                            $hasDebtors = true;
                            $i++;
                            ?>
                            <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0 animate-fadeIn hover:bg-gray-50 px-2 rounded transition-colors duration-200"
                                style="animation-delay: <?php echo 0.6 + ($i * 0.05); ?>s">
                                <a href="musteri_rapor.php?customer=<?php echo $debtor['id']; ?>"
                                    class="text-gray-800 hover:text-primary-600 font-medium transition-colors duration-200 truncate mr-2">
                                    <i class="bi bi-person mr-1"></i> <?php echo htmlspecialchars($debtor['isim']); ?>
                                </a>
                                <span class="badge-danger whitespace-nowrap">
                                    <?php echo number_format($debtor['tutar'], 2, ',', '.'); ?> ₺
                                </span>
                            </div>
                        <?php endforeach; ?>

                        <?php if (!$hasDebtors): ?>
                            <div class="py-8 text-center text-gray-500">
                                <i class="bi bi-emoji-smile text-3xl mb-2 block"></i>
                                Borçlu müşteri bulunmuyor.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-footer">
                    <?php sayfalamaLinkleri($toplamBorcluMusteri, $borcluSayfaBasina, $borcluSayfa, 'borclu_sayfa'); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>