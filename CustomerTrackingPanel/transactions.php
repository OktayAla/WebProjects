<?php require_once __DIR__ . '/includes/auth.php'; require_login(); require_once __DIR__ . '/includes/header.php'; ?>
<?php
	$pdo = get_pdo_connection();
	$customerId = isset($_GET['customer']) ? (int)$_GET['customer'] : 0;

	// Add transaction
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$customer_id = (int)$_POST['customer_id'];
		$type = $_POST['type'] === 'debit' ? 'debit' : 'credit';
		$amount = (float)str_replace([',', ' '], ['.', ''], $_POST['amount']);
		$note = trim($_POST['note']);

		$pdo->beginTransaction();
		try {
			$stmt = $pdo->prepare('INSERT INTO transactions (customer_id, type, amount, note) VALUES (?, ?, ?, ?)');
			$stmt->execute([$customer_id, $type, $amount, $note]);
			if ($type === 'debit') {
				$pdo->prepare('UPDATE customers SET balance = balance + ? WHERE id = ?')->execute([$amount, $customer_id]);
			} else {
				$pdo->prepare('UPDATE customers SET balance = balance - ? WHERE id = ?')->execute([$amount, $customer_id]);
			}
			$pdo->commit();
			header('Location: transactions.php?customer=' . $customer_id);
			exit;
		} catch (Exception $e) {
			$pdo->rollBack();
			$error = 'İşlem başarısız: ' . $e->getMessage();
		}
	}

	$customers = $pdo->query('SELECT id, name FROM customers ORDER BY name ASC')->fetchAll();
	$selectedCustomer = null;
	if ($customerId) {
		$st = $pdo->prepare('SELECT * FROM customers WHERE id = ?');
		$st->execute([$customerId]);
		$selectedCustomer = $st->fetch();
	}
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
					<li aria-current="page">
						<div class="flex items-center">
							<svg class="w-3 h-3 mx-1 text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
								<path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
							</svg>
							<span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">İşlemler</span>
						</div>
					</li>
				</ol>
			</nav>
		</div>
		<a href="customers.php" class="btn-outline-secondary flex items-center space-x-2">
			<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-people" viewBox="0 0 16 16">
				<path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8Zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002a.274.274 0 0 1-.014.002H7.022ZM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM6.936 9.28a5.88 5.88 0 0 0-1.23-.247A7.35 7.35 0 0 0 5 9c-4 0-5 3-5 4 0 .667.333 1 1 1h4.216A2.238 2.238 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816ZM4.92 10A5.493 5.493 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0Zm3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z"/>
			</svg>
			<span>Müşteriler</span>
		</a>
	</div>

	<div class="card mb-6">
		<div class="card-body">
			<?php if (!empty($error)): ?><div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
			<form method="post" class="grid grid-cols-1 md:grid-cols-12 gap-4">
				<div class="md:col-span-4">
					<label class="block mb-2 text-sm font-medium text-gray-900">Müşteri</label>
					<select name="customer_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" required>
						<option value="">Seçiniz</option>
						<?php foreach ($customers as $c): ?>
						<option value="<?php echo $c['id']; ?>" <?php echo $customerId === (int)$c['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['name']); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="md:col-span-2">
					<label class="block mb-2 text-sm font-medium text-gray-900">Tür</label>
					<select name="type" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
						<option value="debit">Borç</option>
						<option value="credit">Tahsilat</option>
					</select>
				</div>
				<div class="md:col-span-2">
					<label class="block mb-2 text-sm font-medium text-gray-900">Tutar</label>
					<input type="text" name="amount" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" placeholder="0,00" required>
				</div>
				<div class="md:col-span-4">
					<label class="block mb-2 text-sm font-medium text-gray-900">Not</label>
					<input type="text" name="note" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" placeholder="İşlem açıklaması">
				</div>
				<div class="md:col-span-12">
					<button class="btn-primary" type="submit">
						<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-lg mr-2" viewBox="0 0 16 16">
							<path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2Z"/>
						</svg>
						Ekle
					</button>
				</div>
			</form>
		</div>
	</div>

	<div class="card">
		<div class="card-header flex justify-between items-center">
			<h5 class="text-lg font-semibold text-gray-900">
				<?php if ($customerId && $selectedCustomer): ?>
					<?php echo htmlspecialchars($selectedCustomer['name']); ?> - İşlem Geçmişi
				<?php else: ?>
					Tüm İşlemler
				<?php endif; ?>
			</h5>
			<?php if ($customerId && $selectedCustomer): ?>
			<a href="customer_report.php?id=<?php echo $customerId; ?>" class="btn-outline-primary flex items-center space-x-2">
				<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-text" viewBox="0 0 16 16">
					<path d="M5.5 7a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5zM5 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5z"/>
					<path d="M9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.5L9.5 0zm0 1v2A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z"/>
				</svg>
				<span>Rapor</span>
			</a>
			<?php endif; ?>
		</div>
		<div class="card-body p-0">
			<div class="overflow-x-auto">
				<table class="w-full text-sm text-left text-gray-500">
					<thead class="text-xs text-gray-700 uppercase bg-gray-50">
						<tr>
							<th scope="col" class="px-4 py-3">#</th>
							<th scope="col" class="px-4 py-3">Müşteri</th>
							<th scope="col" class="px-4 py-3">Tarih</th>
							<th scope="col" class="px-4 py-3">Tutar (₺)</th>
							<th scope="col" class="px-4 py-3">Tür</th>
							<th scope="col" class="px-4 py-3">Açıklama</th>
							<th scope="col" class="px-4 py-3">İşlem</th>
						</tr>
					</thead>
					<tbody>
						<?php
							if ($customerId) {
								$stmt = $pdo->prepare('SELECT t.*, c.name AS customer_name FROM transactions t JOIN customers c ON c.id = t.customer_id WHERE t.customer_id = ? ORDER BY t.created_at DESC');
								$stmt->execute([$customerId]);
							} else {
								$stmt = $pdo->query('SELECT t.*, c.name AS customer_name FROM transactions t JOIN customers c ON c.id = t.customer_id ORDER BY t.created_at DESC');
							}
							$hasTransactions = false;
							foreach ($stmt as $row):
							$hasTransactions = true;
						?>
						<tr class="border-b hover:bg-gray-50 transition-colors">
							<td class="px-4 py-3"><?php echo $row['id']; ?></td>
							<td class="px-4 py-3">
								<?php if (!$customerId): ?>
								<a href="transactions.php?customer=<?php echo $row['customer_id']; ?>" class="font-medium text-primary-600 hover:underline">
									<?php echo htmlspecialchars($row['customer_name']); ?>
								</a>
								<?php else: ?>
									<?php echo htmlspecialchars($row['customer_name']); ?>
								<?php endif; ?>
							</td>
							<td class="px-4 py-3"><?php echo date('d.m.Y H:i', strtotime($row['created_at'])); ?></td>
							<td class="px-4 py-3 font-medium"><?php echo number_format($row['amount'], 2, ',', '.'); ?></td>
							<td class="px-4 py-3">
								<?php if ($row['type'] === 'debit'): ?>
								<span class="badge-debit">Borç</span>
								<?php else: ?>
								<span class="badge-credit">Tahsilat</span>
								<?php endif; ?>
							</td>
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
						
						<?php if (!$hasTransactions): ?>
						<tr>
							<td colspan="7" class="px-4 py-6 text-center text-gray-500">
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


