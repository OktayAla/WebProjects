<?php require_once __DIR__ . '/includes/auth.php'; require_login(); require_once __DIR__ . '/includes/header.php'; ?>
<?php
	$pdo = get_pdo_connection();
	$totalCustomers = (int)$pdo->query('SELECT COUNT(*) FROM customers')->fetchColumn();
	$totalSales = (float)$pdo->query("SELECT COALESCE(SUM(amount),0) FROM transactions WHERE type = 'debit'")->fetchColumn();
	$totalCollections = (float)$pdo->query("SELECT COALESCE(SUM(amount),0) FROM transactions WHERE type = 'credit'")->fetchColumn();
	$totalReceivables = (float)$pdo->query('SELECT COALESCE(SUM(balance),0) FROM customers')->fetchColumn();
?>

	<nav aria-label="breadcrumb" class="mb-6">
		<ol class="flex items-center space-x-2 text-sm text-gray-600">
			<li><a href="index.php" class="hover:text-primary-600 transition-colors duration-200">Panel</a></li>
			<li class="flex items-center">
				<svg class="w-4 h-4 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
				<span class="font-medium text-gray-900">Genel Bakış</span>
			</li>
		</ol>
	</nav>

	<!-- Stats Cards -->
	<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
		<!-- Total Customers Card -->
		<div class="bg-white rounded-xl shadow-sm p-6 card-hover animate-slideInUp" style="animation-delay: 0.1s">
			<div class="flex items-center">
				<div class="flex-shrink-0 bg-primary-100 p-3 rounded-full">
					<i class="bi bi-people-fill text-primary-600 text-xl"></i>
				</div>
				<div class="ml-4">
					<p class="text-sm font-medium text-gray-500">Toplam Müşteri</p>
					<p class="text-2xl font-semibold text-gray-900"><?php echo $totalCustomers; ?></p>
				</div>
			</div>
		</div>

		<!-- Total Sales Card -->
		<div class="bg-white rounded-xl shadow-sm p-6 card-hover animate-slideInUp" style="animation-delay: 0.2s">
			<div class="flex items-center">
				<div class="flex-shrink-0 bg-primary-100 p-3 rounded-full">
					<i class="bi bi-bag-fill text-primary-600 text-xl"></i>
				</div>
				<div class="ml-4">
					<p class="text-sm font-medium text-gray-500">Toplam Satış</p>
					<p class="text-2xl font-semibold text-gray-900"><?php echo number_format($totalSales, 2, ',', '.'); ?> ₺</p>
				</div>
			</div>
		</div>

		<!-- Total Collections Card -->
		<div class="bg-white rounded-xl shadow-sm p-6 card-hover animate-slideInUp" style="animation-delay: 0.3s">
			<div class="flex items-center">
				<div class="flex-shrink-0 bg-success-100 p-3 rounded-full">
					<i class="bi bi-cash-coin text-success-600 text-xl"></i>
				</div>
				<div class="ml-4">
					<p class="text-sm font-medium text-gray-500">Toplam Tahsilat</p>
					<p class="text-2xl font-semibold text-gray-900"><?php echo number_format($totalCollections, 2, ',', '.'); ?> ₺</p>
				</div>
			</div>
		</div>

		<!-- Total Receivables Card -->
		<div class="bg-white rounded-xl shadow-sm p-6 card-hover animate-slideInUp" style="animation-delay: 0.4s">
			<div class="flex items-center">
				<div class="flex-shrink-0 bg-danger-100 p-3 rounded-full">
					<i class="bi bi-clipboard2-data-fill text-danger-600 text-xl"></i>
				</div>
				<div class="ml-4">
					<p class="text-sm font-medium text-gray-500">Toplam Alacak</p>
					<p class="text-2xl font-semibold text-gray-900"><?php echo number_format($totalReceivables, 2, ',', '.'); ?> ₺</p>
				</div>
			</div>
		</div>
	</div>

	<!-- Dashboard Content -->
	<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
		<!-- Recent Sales Table -->
		<div class="lg:col-span-2">
			<div class="bg-white rounded-xl shadow-sm overflow-hidden animate-fadeIn" style="animation-delay: 0.5s">
				<div class="p-6">
					<div class="flex justify-between items-center mb-4">
						<h3 class="text-lg font-semibold text-gray-900">Son Satışlar</h3>
						<a href="transactions.php" class="text-sm text-primary-600 hover:text-primary-800 flex items-center transition-colors duration-200">
							Tümünü Gör <i class="bi bi-chevron-right ml-1"></i>
						</a>
					</div>
					<div class="overflow-x-auto">
						<table class="min-w-full divide-y divide-gray-200">
							<thead class="bg-gray-50">
								<tr>
									<th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
									<th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Müşteri</th>
									<th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tarih</th>
									<th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tutar</th>
									<th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Not</th>
									<th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">İşlem</th>
								</tr>
							</thead>
							<tbody class="bg-white divide-y divide-gray-200">
								<?php
									$stmt = $pdo->query("SELECT t.id, t.customer_id, c.name AS customer_name, t.amount, t.note, t.created_at FROM transactions t JOIN customers c ON c.id = t.customer_id WHERE t.type='debit' ORDER BY t.created_at DESC LIMIT 10");
									$i = 0;
									foreach ($stmt as $row):
									$i++;
								?>
								<tr class="hover:bg-gray-50 animate-fadeIn" style="animation-delay: <?php echo 0.5 + ($i * 0.05); ?>s">
									<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500"><?php echo $row['id']; ?></td>
									<td class="px-4 py-3 whitespace-nowrap">
										<a href="customer_report.php?customer=<?php echo (int)$row['customer_id']; ?>" class="text-primary-600 hover:text-primary-900 transition-colors duration-200">
											<?php echo htmlspecialchars($row['customer_name']); ?>
										</a>
									</td>
									<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
										<?php echo date('d.m.Y H:i', strtotime($row['created_at'])); ?>
									</td>
									<td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
										<?php echo number_format($row['amount'], 2, ',', '.'); ?> ₺
									</td>
									<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
										<?php echo htmlspecialchars($row['note']); ?>
									</td>
									<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
										<a href="print.php?id=<?php echo $row['id']; ?>" class="text-gray-600 hover:text-gray-900 transition-colors duration-200" data-tooltip="Yazdır">
											<i class="bi bi-printer"></i>
										</a>
									</td>
								</tr>
								<?php endforeach; ?>
								<?php if ($i === 0): ?>
								<tr>
									<td colspan="6" class="px-4 py-8 text-center text-gray-500">
										<i class="bi bi-inbox text-3xl mb-2 block"></i>
										Henüz satış kaydı bulunmuyor.
									</td>
								</tr>
								<?php endif; ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>

		<!-- Customers with Debt -->
		<div class="lg:col-span-1">
			<div class="bg-white rounded-xl shadow-sm overflow-hidden animate-fadeIn" style="animation-delay: 0.6s">
				<div class="p-6">
					<div class="flex justify-between items-center mb-4">
						<h3 class="text-lg font-semibold text-gray-900">Borçlu Müşteriler</h3>
						<a href="customers.php" class="text-sm text-primary-600 hover:text-primary-800 flex items-center transition-colors duration-200">
							Tümünü Gör <i class="bi bi-chevron-right ml-1"></i>
						</a>
					</div>
					<div class="space-y-3">
						<?php 
						$debtors = $pdo->query('SELECT id, name, balance FROM customers WHERE balance > 0 ORDER BY balance DESC LIMIT 10');
						$hasDebtors = false;
						$i = 0;
						foreach ($debtors as $debtor): 
						$hasDebtors = true;
						$i++;
						?>
						<div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0 animate-fadeIn" style="animation-delay: <?php echo 0.6 + ($i * 0.05); ?>s">
							<a href="customer_report.php?customer=<?php echo $debtor['id']; ?>" class="text-gray-800 hover:text-primary-600 font-medium transition-colors duration-200">
								<?php echo htmlspecialchars($debtor['name']); ?>
							</a>
							<span class="bg-danger-500 text-white text-xs px-2 py-1 rounded-full">
								<?php echo number_format($debtor['balance'], 2, ',', '.'); ?> ₺
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
			</div>

			<!-- Quick Actions -->
			<div class="bg-white rounded-xl shadow-sm overflow-hidden mt-6 animate-fadeIn" style="animation-delay: 0.7s">
				<div class="p-6">
					<h3 class="text-lg font-semibold text-gray-900 mb-4">Hızlı Erişim</h3>
					<div class="grid grid-cols-2 gap-3">
						<a href="customers.php" class="flex flex-col items-center justify-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 hover:scale-105 transition-all duration-200">
							<i class="bi bi-person-plus text-2xl text-primary-600 mb-2"></i>
							<span class="text-sm font-medium text-gray-700">Yeni Müşteri</span>
						</a>
						<a href="transactions.php" class="flex flex-col items-center justify-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 hover:scale-105 transition-all duration-200">
							<i class="bi bi-cart-plus text-2xl text-primary-600 mb-2"></i>
							<span class="text-sm font-medium text-gray-700">Yeni Satış</span>
						</a>
						<a href="transactions.php" class="flex flex-col items-center justify-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 hover:scale-105 transition-all duration-200">
							<i class="bi bi-cash text-2xl text-success-600 mb-2"></i>
							<span class="text-sm font-medium text-gray-700">Tahsilat</span>
						</a>
						<a href="customers.php" class="flex flex-col items-center justify-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 hover:scale-105 transition-all duration-200">
							<i class="bi bi-search text-2xl text-primary-600 mb-2"></i>
							<span class="text-sm font-medium text-gray-700">Müşteri Ara</span>
						</a>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>


