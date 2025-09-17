<!-- Bu sistem Oktay ALA tarafından, Analiz Tarım için geliştirilmiştir. -->
<!-- Copyright © Her Hakkı Saklıdır. Ticari amaçlı kullanılması yasaktır. -->
 
<?php  require_once __DIR__ . '/includes/auth.php'; require_login(); ?>
<?php
	$pdo = get_pdo_connection();
	$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
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
				size: auto;
				margin: 10mm;
			}
			body {
				print-color-adjust: exact;
				-webkit-print-color-adjust: exact;
			}
		}
	</style>
	<script>
		function yazdir() {
		  setTimeout(function() {
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
				<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer" viewBox="0 0 16 16">
					<path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
					<path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>
				</svg>
				<span>Yazdır</span>
			</a>
			<a href="index.php" class="btn btn-outline flex items-center space-x-2">
				<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
					<path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
				</svg>
				<span>Panele Dön</span>
			</a>
		</div>
		<div class="card-hover">
			<div class="card-header border-b border-gray-200 bg-white py-3 px-4">
				<div class="flex justify-between items-center">
					<h4 class="text-lg font-semibold text-gray-900">
						<?php echo $tx['odeme_tipi'] === 'borc' ? 'Borç Dekontu' : 'Tahsilat Dekontu'; ?>
					</h4>
					<button onclick="toggleCustomize()" class="btn btn-outline btn-sm no-print">
						<i class="bi bi-gear mr-1"></i> Özelleştir
					</button>
				</div>
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
				
				<!-- Şirket Bilgileri -->
				<div id="company-info" class="mb-6 text-center">
					<div id="company-contact-display" class="text-sm text-gray-600 mt-1"></div>
				</div>
				
				<div class="flex justify-center mb-4">
					<img src="img/logo.jpg" alt="Logo" style="width:150px;">
				</div>
				<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
					<div>
						<h5 class="text-base font-semibold text-gray-900 mb-3">Müşteri Bilgileri</h5>
						<div class="space-y-2">
							<div class="flex flex-col">
								<span class="text-sm font-medium text-gray-500">Ad Soyad</span>
								<span class="text-base"><?php echo htmlspecialchars($tx['musteri_isim']); ?></span>
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
								<span class="text-base"><?php echo date('d.m.Y H:i', strtotime($tx['olusturma_zamani'])); ?></span>
							</div>
							<div class="flex flex-col md:items-end">
								<span class="text-sm font-medium text-gray-500">Tür</span>
								<span class="<?php echo $tx['odeme_tipi'] === 'debit' ? 'badge badge-debit' : 'badge badge-credit'; ?>">
									<?php echo $tx['odeme_tipi'] === 'debit' ? 'Borç' : 'Tahsilat'; ?>
								</span>
							</div>
							<div class="flex flex-col md:items-end">
								<span class="text-sm font-medium text-gray-500">Tutar</span>
								<span class="text-2xl font-bold"><?php echo number_format($tx['miktar'], 2, ',', '.'); ?> ₺</span>
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
			</div>
		</div>
	</div>
</body>
</html>