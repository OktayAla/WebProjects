<?php require_once __DIR__ . '/includes/auth.php'; require_login(); ?>
<?php
	$pdo = get_pdo_connection();
	$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
	$stmt = $pdo->prepare('SELECT t.*, c.name AS customer_name, c.phone, c.address, p.name AS product_name FROM transactions t JOIN customers c ON c.id = t.customer_id LEFT JOIN products p ON p.id = t.product_id WHERE t.id = ?');
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
	<title>Yazdır - <?php echo APP_NAME; ?></title>
	<script src="https://cdn.tailwindcss.com"></script>
	<script>
		tailwind.config = {
			theme: {
				extend: {
					colors: {
						primary: {
							50: '#f0f9ff',
							100: '#e0f2fe',
							200: '#bae6fd',
							300: '#7dd3fc',
							400: '#38bdf8',
							500: '#0ea5e9',
							600: '#0284c7',
							700: '#0369a1',
							800: '#075985',
							900: '#0c4a6e',
							950: '#082f49',
						},
					},
				},
			},
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
</head>
<body class="bg-gray-50">
	<div class="container mx-auto px-4 py-8 max-w-4xl">
		<div class="flex justify-between items-center mb-6 no-print">
			<a href="javascript:window.print();" class="btn btn-primary flex items-center space-x-2">
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
				<h4 class="text-lg font-semibold text-gray-900">
					<?php echo $tx['type'] === 'debit' ? 'Borç Dekontu' : 'Tahsilat Dekontu'; ?>
				</h4>
			</div>
			<div class="p-6">
				<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
					<div>
						<h5 class="text-base font-semibold text-gray-900 mb-3">Müşteri Bilgileri</h5>
						<div class="space-y-2">
							<div class="flex flex-col">
								<span class="text-sm font-medium text-gray-500">Ad Soyad</span>
								<span class="text-base"><?php echo htmlspecialchars($tx['customer_name']); ?></span>
							</div>
							<div class="flex flex-col">
								<span class="text-sm font-medium text-gray-500">Telefon</span>
								<span class="text-base"><?php echo htmlspecialchars($tx['phone']); ?></span>
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
								<span class="text-base"><?php echo date('d.m.Y H:i', strtotime($tx['created_at'])); ?></span>
							</div>
							<div class="flex flex-col md:items-end">
								<span class="text-sm font-medium text-gray-500">Tür</span>
								<span class="<?php echo $tx['type'] === 'debit' ? 'badge badge-debit' : 'badge badge-credit'; ?>">
									<?php echo $tx['type'] === 'debit' ? 'Borç' : 'Tahsilat'; ?>
								</span>
							</div>
							<div class="flex flex-col md:items-end">
								<span class="text-sm font-medium text-gray-500">Tutar</span>
								<span class="text-2xl font-bold"><?php echo number_format($tx['amount'], 2, ',', '.'); ?> ₺</span>
							</div>
						</div>
					</div>
				</div>
				<div class="border-t border-gray-200 mt-6 pt-6">
					<h5 class="text-base font-semibold text-gray-900 mb-2">Açıklama</h5>
					<div class="bg-gray-50 p-4 rounded-lg whitespace-pre-line">
						<?php echo htmlspecialchars($tx['note'] ?: 'Açıklama bulunmuyor.'); ?>
					</div>
				</div>
				
				<?php if ($tx['product_name']): ?>
				<div class="border-t border-gray-200 mt-6 pt-6">
					<h5 class="text-base font-semibold text-gray-900 mb-2">Ürün Bilgisi</h5>
					<div class="bg-blue-50 p-4 rounded-lg">
						<div>
							<span class="text-sm font-medium text-gray-500">Ürün Adı</span>
							<div class="text-base font-medium"><?php echo htmlspecialchars($tx['product_name']); ?></div>
						</div>
					</div>
				</div>
				<?php endif; ?>

			</div>
		</div>
	</div>
</body>
</html>

