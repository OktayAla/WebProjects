<!-- Bu sistem Oktay ALA tarafından, Analiz Tarım için geliştirilmiştir. -->
<!-- Copyright © Her Hakkı Saklıdır. Ticari amaçlı kullanılması yasaktır. -->

<?php require_once __DIR__ . '/includes/auth.php';
require_login(); ?>
<?php
$pdo = get_pdo_connection();
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$stmt = $pdo->prepare('
        SELECT 
            i.*, 
            m.isim AS musteri_isim, 
            m.numara, 
            m.adres, 
            u.isim AS urun_isim 
        FROM islemler i 
        JOIN musteriler m ON m.id = i.musteri_id 
        LEFT JOIN urunler u ON u.id = i.urun_id 
        WHERE i.id = ?
    ');
$stmt->execute([$id]);
$tx = $stmt->fetch();
if (!$tx) {
	die('Kayıt bulunamadı');
}

$customerId = isset($_GET['customer']) ? (int) $_GET['customer'] : (isset($tx['musteri_id']) ? (int) $tx['musteri_id'] : 0);
$customer = null;
if ($customerId) {
	$stmt = $pdo->prepare('SELECT * FROM musteriler WHERE id = ?');
	$stmt->execute([$customerId]);
	$customer = $stmt->fetch();
}


// Toplam Borç (Satış)
$salesStmt = $pdo->prepare("SELECT COALESCE(SUM(miktar), 0) FROM islemler WHERE musteri_id = ? AND odeme_tipi = 'borc'");
$salesStmt->execute([$customerId]);
$totalSales = (float) $salesStmt->fetchColumn();

// Toplam Tahsilat (Ödeme)
$paidStmt = $pdo->prepare("SELECT COALESCE(SUM(miktar), 0) FROM islemler WHERE musteri_id = ? AND odeme_tipi = 'tahsilat'");
$paidStmt->execute([$customerId]);
$totalPaid = (float) $paidStmt->fetchColumn();

// Net Bakiye (Tahsilat - Borç) -> Pozitif: alacaklı, Negatif: borçlu
$remaining = $totalPaid - $totalSales;

$historyStmt = $pdo->prepare('SELECT i.id, i.odeme_tipi, i.miktar, i.aciklama, i.olusturma_zamani, u.isim AS urun_isim FROM islemler i LEFT JOIN urunler u ON u.id = i.urun_id WHERE i.musteri_id = ? ORDER BY i.olusturma_zamani DESC');
$historyStmt->execute([$customerId]);
$history = $historyStmt->fetchAll();
?>


<!DOCTYPE html>
<html lang="tr">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Yazdır</title>
	<script src="https://cdn.tailwindcss.com"></script>
	<script>
		tailwind.config = {
			theme: {
				extend: {
					colors: {
						primary: {
							50: '#eef2ff',
							100: '#e0e7ff',
							200: '#c7d2fe',
							300: '#a5b4fc',
							400: '#818cf8',
							500: '#6366f1',
							600: '#4f46e5',
							700: '#4338ca',
							800: '#3730a3',
							900: '#312e81',
							950: '#1e1b4b',
						},
					}
				}
			}
		}
	</script>
	<style>
		@media print {
			.no-print {
				display: none !important;
			}

			@page {
				size: A5 portrait;
				margin: 5mm;
			}

			body {
				print-color-adjust: exact;
				-webkit-print-color-adjust: exact;
				font-size: 12px;
			}
		}

		.receipt {
			max-width: 148mm;
			margin: 0 auto;
			padding: 10mm;
			border: 1px solid #ddd;
			box-shadow: 0 0 10px rgba(0,0,0,0.1);
			background: white;
		}

		.receipt-header {
			text-align: center;
			border-bottom: 2px solid #000;
			padding-bottom: 10px;
			margin-bottom: 20px;
		}

		.receipt-logo {
			max-width: 100px;
			margin: 0 auto 10px;
		}

		.receipt-title {
			font-size: 18px;
			font-weight: bold;
			margin: 10px 0;
			text-transform: uppercase;
		}

		.receipt-details {
			display: grid;
			grid-template-columns: repeat(2, 1fr);
			gap: 15px;
			margin-bottom: 20px;
		}

		.receipt-row {
			display: flex;
			justify-content: space-between;
			padding: 5px 0;
			border-bottom: 1px dotted #ddd;
		}

		.receipt-footer {
			margin-top: 30px;
			padding-top: 20px;
			border-top: 1px solid #000;
			text-align: center;
		}

		.signature-area {
			display: grid;
			grid-template-columns: 1fr 1fr;
			gap: 20px;
			margin-top: 30px;
			padding-top: 20px;
		}

		.signature-box {
			border-top: 1px solid #000;
			padding-top: 5px;
			text-align: center;
		}
	</style>
	<script>
		function yazdir() {
			setTimeout(function () {
				window.print();
			}, 500);
		}

		function toggleCustomize() {
			const panel = document.getElementById('customize-panel');
			panel.classList.toggle('hidden');
		}

		function applyCustomization() {
			const companyName = document.getElementById('company-name').value;
			const companyPhone = document.getElementById('company-phone').value;
			const companyEmail = document.getElementById('company-email').value;
			const companyAddress = document.getElementById('company-address').value;

			// Şirket adını güncelle
			document.getElementById('company-name-display').textContent = companyName;

			// İletişim bilgilerini güncelle
			const contactInfo = [];
			if (companyPhone) contactInfo.push(`Tel: ${companyPhone}`);
			if (companyEmail) contactInfo.push(`E-posta: ${companyEmail}`);
			if (companyAddress) contactInfo.push(`Adres: ${companyAddress}`);

			document.getElementById('company-contact-display').innerHTML = contactInfo.join('<br>');

			// Paneli gizle
			document.getElementById('customize-panel').classList.add('hidden');
		}

		function resetCustomization() {
			document.getElementById('company-name').value = 'Analiz Tarım';
			document.getElementById('company-phone').value = '';
			document.getElementById('company-email').value = '';
			document.getElementById('company-address').value = '';

			applyCustomization();
		}
	</script>
</head>

<body class="bg-gray-50">
	<div class="container mx-auto px-4 py-8 max-w-4xl">
		<div class="flex justify-between items-center mb-6 no-print">
			<a href="javascript:yazdir();" class="btn btn-primary flex items-center space-x-2">
				<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer"
					viewBox="0 0 16 16">
					<path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z" />
					<path
						d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z" />
				</svg>
				<span>Yazdır</span>
			</a>
			<a href="index.php" class="btn btn-outline flex items-center space-x-2">
				<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
					class="bi bi-arrow-left" viewBox="0 0 16 16">
					<path fill-rule="evenodd"
						d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z" />
				</svg>
				<span>Panele Dön</span>
			</a>
		</div>
		<div class="receipt">
			<div class="receipt-header">
				<img src="img/logo.jpg" alt="Logo" class="receipt-logo">
				<h1 id="company-name-display" class="receipt-title">ANALİZ TARIM</h1>
				<div id="company-contact-display" class="text-sm text-gray-600"></div>
				<h2 class="receipt-title">
					<?php echo $tx['odeme_tipi'] === 'borc' ? 'BORÇ DEKONTU' : 'TAHSİLAT DEKONTU'; ?>
				</h2>
				<div class="text-sm">Fiş No: #<?php echo $tx['id']; ?></div>
				<div class="text-sm">Tarih: <?php echo date('d.m.Y H:i', strtotime($tx['olusturma_zamani'])); ?></div>
			</div>
			<div class="p-6">
				<!-- Özelleştirme Paneli -->
				<div id="customize-panel" class="mb-6 p-4 bg-gray-50 rounded-lg no-print hidden">
					<h5 class="font-semibold mb-3">Dekont Özelleştirme</h5>
					<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
						<div>
							<label class="block text-sm font-medium mb-1">Şirket Adı</label>
							<input type="text" id="company-name" class="form-input" value="Müşteri Portalı">
						</div>
						<div>
							<label class="block text-sm font-medium mb-1">Telefon</label>
							<input type="text" id="company-phone" class="form-input" value="">
						</div>
						<div>
							<label class="block text-sm font-medium mb-1">E-posta</label>
							<input type="email" id="company-email" class="form-input" value="">
						</div>
						<div>
							<label class="block text-sm font-medium mb-1">Adres</label>
							<textarea id="company-address" class="form-input" rows="2"></textarea>
						</div>
					</div>
					<div class="mt-4">
						<button onclick="applyCustomization()" class="btn btn-primary btn-sm">
							<i class="bi bi-check mr-1"></i> Uygula
						</button>
						<button onclick="resetCustomization()" class="btn btn-outline btn-sm ml-2">
							<i class="bi bi-arrow-clockwise mr-1"></i> Sıfırla
						</button>
					</div>
				</div>

				<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
					<div class="stat-card card-hover">
						<div class="stat-icon" style="background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);">
							<i class="bi bi-cash-coin"></i>
						</div>

					</div>
					<div class="stat-card card-hover">
						<div class="stat-icon" style="background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);">
							<i class="bi bi-wallet2"></i>
						</div>
					</div>
				</div>
				<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
					<div>
						<h5 class="text-base font-semibold text-gray-900 mb-3">Müşteri Bilgileri</h5>
						<div class="space-y-2">
							<div class="flex flex-col">
								<span class="text-sm font-medium text-gray-500">Ad Soyad</span>
								<span class="text-base"><?php echo htmlspecialchars($tx['musteri_isim']); ?></span>

								<div class="stat-info">
									<span class="stat-label">Net Bakiye</span>
									<span
										class="stat-value <?php echo $remaining < 0 ? 'text-danger-600' : ($remaining > 0 ? 'text-success-600' : ''); ?>">
										<?php echo ($remaining > 0 ? '+' : '') . number_format($remaining, 2, ',', '.'); ?>
										₺
									</span>

								</div>
							</div>
						</div>
					</div>
					<div class="md:text-right">
						<h5 class="text-base font-semibold text-gray-900 mb-3">İşlem Detayları</h5>
						<div class="space-y-2">
							<div class="flex flex-col md:items-end">
								<span class="text-sm font-medium text-gray-500">İşlem No</span>
								<span class="text-base">#<?php echo $tx['id']; ?></span>
							</div>
							<div class="flex flex-col md:items-end">
								<span class="text-sm font-medium text-gray-500">Tarih</span>
								<span
									class="text-base"><?php echo date('d.m.Y H:i', strtotime($tx['olusturma_zamani'])); ?></span>
							</div>
							<div class="flex flex-col md:items-end">
								<span class="text-sm font-medium text-gray-500">Tür</span>
								<span
									class="<?php echo $tx['odeme_tipi'] === 'debit' ? 'badge badge-debit' : 'badge badge-credit'; ?>">
									<?php echo $tx['odeme_tipi'] === 'debit' ? 'Borç' : 'Tahsilat'; ?>
								</span>
							</div>
							<div class="flex flex-col md:items-end">
								<span class="text-sm font-medium text-gray-500">Tutar</span>
								<span
									class="text-2xl font-bold"><?php echo number_format($tx['miktar'], 2, ',', '.'); ?>
									₺</span>
							</div>
						</div>
					</div>
				</div>
				<div class="border-t border-gray-200 mt-6 pt-6">
					<h5 class="text-base font-semibold text-gray-900 mb-2">Açıklama</h5>
					<div class="bg-gray-50 p-4 rounded-lg whitespace-pre-line">
						<?php echo htmlspecialchars($tx['aciklama'] ?: 'Açıklama bulunmuyor.'); ?>
					</div>
				</div>

				<?php if ($tx['urun_isim']): ?>
					<div class="border-t border-gray-200 mt-6 pt-6">
						<h5 class="text-base font-semibold text-gray-900 mb-2">Ürün Bilgisi</h5>
						<div class="bg-blue-50 p-4 rounded-lg">
							<div>
								<span class="text-sm font-medium text-gray-500">Ürün Adı</span>
								<div class="text-base font-medium"><?php echo htmlspecialchars($tx['urun_isim']); ?></div>
							</div>
						</div>
					</div>
				<?php endif; ?>

				<div class="receipt-footer">
					<div class="text-xs text-gray-500">Bu belge <?php echo date('d.m.Y H:i'); ?> tarihinde oluşturulmuştur.</div>
				</div>
			</div>
		</div>
	</div>
</body>

</html>