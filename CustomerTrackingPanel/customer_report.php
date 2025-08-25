<?php require_once __DIR__ . '/includes/auth.php'; require_login(); require_once __DIR__ . '/includes/header.php'; ?>
<?php
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

	<div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
		<div class="flex items-center space-x-2">
			<nav class="flex" aria-label="Breadcrumb">
				<ol class="inline-flex items-center space-x-1 md:space-x-3">
					<li class="inline-flex items-center">
						<a href="index.php" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary-600">
							<svg class="w-3 h-3 mr-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
								<path d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
							</svg>
							Panel
						</a>
					</li>
					<li>
						<div class="flex items-center">
							<svg class="w-3 h-3 mx-1 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
								<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
							</svg>
							<a href="customers.php" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-600 md:ml-2">Müşteriler</a>
						</div>
					</li>
					<li aria-current="page">
						<div class="flex items-center">
							<svg class="w-3 h-3 mx-1 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
								<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
							</svg>
							<span class="ml-1 text-sm font-medium text-gray-500 md:ml-2"><?php echo htmlspecialchars($customer['name']); ?> - Rapor</span>
						</div>
					</li>
				</ol>
			</nav>
		</div>
		<div class="flex space-x-2">
			<a href="transactions.php?customer=<?php echo $customerId; ?>" class="btn-outline-secondary flex items-center space-x-2">
				<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
					<path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
				</svg>
				<span>İşlemlere Dön</span>
			</a>
			<button onclick="window.print()" class="btn-outline-primary flex items-center space-x-2">
				<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-printer" viewBox="0 0 16 16">
					<path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
					<path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>
				</svg>
				<span>Yazdır</span>
			</button>
		</div>
	</div>

	<div class="grid grid-cols-1 md:grid-cols-12 gap-6">
		<div class="md:col-span-5">
			<div class="card">
				<div class="card-header">
					<h5 class="text-lg font-semibold text-gray-900">Müşteri Bilgileri</h5>
				</div>
				<div class="card-body">
					<div class="grid grid-cols-1 gap-4">
						<div class="flex flex-col">
							<span class="text-sm font-medium text-gray-500">Ad</span>
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
		<div class="md:col-span-7">
			<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
				<div class="stat-card">
					<div class="stat-card-body">
						<div class="flex items-center justify-between">
							<div>
								<div class="text-sm font-medium text-gray-500">Toplam Satış</div>
								<div class="text-2xl font-bold text-gray-900 mt-1"><?php echo number_format($totalSales, 2, ',', '.'); ?> ₺</div>
							</div>
							<div class="p-3 rounded-full bg-primary-100 text-primary-600">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-cart" viewBox="0 0 16 16">
									<path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l1.313 7h8.17l1.313-7H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
								</svg>
							</div>
						</div>
					</div>
				</div>
				<div class="stat-card">
					<div class="stat-card-body">
						<div class="flex items-center justify-between">
							<div>
								<div class="text-sm font-medium text-gray-500">Ödenen Toplam</div>
								<div class="text-2xl font-bold text-gray-900 mt-1"><?php echo number_format($totalPaid, 2, ',', '.'); ?> ₺</div>
							</div>
							<div class="p-3 rounded-full bg-green-100 text-green-600">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-cash" viewBox="0 0 16 16">
									<path d="M8 10a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/>
									<path d="M0 4a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1V4zm3 0a2 2 0 0 1-2 2v4a2 2 0 0 1 2 2h10a2 2 0 0 1 2-2V6a2 2 0 0 1-2-2H3z"/>
								</svg>
							</div>
						</div>
					</div>
				</div>
				<div class="stat-card">
					<div class="stat-card-body">
						<div class="flex items-center justify-between">
							<div>
								<div class="text-sm font-medium text-gray-500">Kalan Borç</div>
								<div class="text-2xl font-bold text-gray-900 mt-1"><?php echo number_format($remaining, 2, ',', '.'); ?> ₺</div>
							</div>
							<div class="p-3 rounded-full bg-red-100 text-red-600">
								<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-wallet2" viewBox="0 0 16 16">
									<path d="M12.136.326A1.5 1.5 0 0 1 14 1.78V3h.5A1.5 1.5 0 0 1 16 4.5v9a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 13.5v-9a1.5 1.5 0 0 1 1.432-1.499L12.136.326zM5.562 3H13V1.78a.5.5 0 0 0-.621-.484L5.562 3zM1.5 4a.5.5 0 0 0-.5.5v9a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5h-13z"/>
								</svg>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="card mt-6">
		<div class="card-header">
			<h5 class="text-lg font-semibold text-gray-900">İşlem Geçmişi (<?php echo count($history); ?> adet)</h5>
		</div>
		<div class="card-body p-0">
			<div class="overflow-x-auto">
				<table class="w-full text-sm text-left text-gray-500">
					<thead class="text-xs text-gray-700 uppercase bg-gray-50">
						<tr>
							<th scope="col" class="px-4 py-3">#</th>
							<th scope="col" class="px-4 py-3">Tarih</th>
							<th scope="col" class="px-4 py-3">Tür</th>
							<th scope="col" class="px-4 py-3">Tutar (₺)</th>
							<th scope="col" class="px-4 py-3">Not</th>
							<th scope="col" class="px-4 py-3">İşlem</th>
						</tr>
					</thead>
					<tbody>
						<?php if (count($history) > 0): ?>
							<?php foreach ($history as $row): ?>
							<tr class="border-b hover:bg-gray-50 transition-colors">
								<td class="px-4 py-3"><?php echo $row['id']; ?></td>
								<td class="px-4 py-3"><?php echo date('d.m.Y H:i', strtotime($row['created_at'])); ?></td>
								<td class="px-4 py-3">
									<?php if ($row['type'] === 'debit'): ?>
									<span class="badge-debit">Borç</span>
									<?php else: ?>
									<span class="badge-credit">Tahsilat</span>
									<?php endif; ?>
								</td>
								<td class="px-4 py-3 font-medium"><?php echo number_format($row['amount'], 2, ',', '.'); ?></td>
								<td class="px-4 py-3"><?php echo htmlspecialchars($row['note']); ?></td>
								<td class="px-4 py-3">
									<a href="print.php?id=<?php echo $row['id']; ?>" class="btn-outline-secondary btn-sm flex items-center space-x-1">
										<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-printer" viewBox="0 0 16 16">
											<path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
											<path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>
										</svg>
										<span>Yazdır</span>
									</a>
								</td>
							</tr>
							<?php endforeach; ?>
						<?php else: ?>
						<tr>
							<td colspan="6" class="px-4 py-6 text-center text-gray-500">
								<div class="flex flex-col items-center justify-center">
									<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-inbox mb-2 text-gray-400" viewBox="0 0 16 16">
										<path d="M4.98 4a.5.5 0 0 0-.39.188L1.54 8H6a.5.5 0 0 1 .5.5 1.5 1.5 0 1 0 3 0A.5.5 0 0 1 10 8h4.46l-3.05-3.812A.5.5 0 0 0 11.02 4H4.98zm-1.17-.437A1.5 1.5 0 0 1 4.98 3h6.04a1.5 1.5 0 0 1 1.17.563l3.7 4.625a.5.5 0 0 1 .106.374l-.39 3.124A1.5 1.5 0 0 1 14.117 13H1.883a1.5 1.5 0 0 1-1.489-1.314l-.39-3.124a.5.5 0 0 1 .106-.374l3.7-4.625z"/>
									</svg>
									<p>Henüz işlem kaydı bulunmuyor</p>
								</div>
							</td>
						</tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>


