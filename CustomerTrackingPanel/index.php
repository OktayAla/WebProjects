<?php 
require_once __DIR__ . '/includes/auth.php'; 
require_login(); 
require_once __DIR__ . '/includes/header.php'; 

$pdo = get_pdo_connection();
$totalCustomers = (int)$pdo->query('SELECT COUNT(*) FROM customers')->fetchColumn();
$totalSales = (float)$pdo->query("SELECT COALESCE(SUM(amount),0) FROM transactions WHERE type = 'debit'")->fetchColumn();
$totalCollections = (float)$pdo->query("SELECT COALESCE(SUM(amount),0) FROM transactions WHERE type = 'credit'")->fetchColumn();
$totalReceivables = (float)$pdo->query('SELECT COALESCE(SUM(balance),0) FROM customers')->fetchColumn();
?>

<!-- Floating background elements -->
<div class="floating-element"></div>
<div class="floating-element"></div>
<div class="floating-element"></div>

<div class="dashboard-header">
	<div class="container mx-auto px-4">
		<h1 class="dashboard-title">Genel Bakış</h1>
		<p class="dashboard-subtitle">Sistem istatistikleriniz ve son işlemler</p>
	</div>
</div>

<div class="container mx-auto px-4 py-6">
	<!-- Stats Cards -->
	<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
		<!-- Total Customers Card -->
		<div class="stat-card card-hover animate-slideInUp" style="animation-delay: 0.1s">
			<div class="stat-icon">
				<i class="bi bi-people-fill"></i>
			</div>
			<div class="stat-info">
				<span class="stat-label">Toplam Müşteri</span>
				<span class="stat-value"><?php echo $totalCustomers; ?></span>
			</div>
		</div>

		<!-- Total Sales Card -->
		<div class="stat-card card-hover animate-slideInUp" style="animation-delay: 0.2s">
			<div class="stat-icon">
				<i class="bi bi-bag-fill"></i>
			</div>
			<div class="stat-info">
				<span class="stat-label">Toplam Satış</span>
				<span class="stat-value"><?php echo number_format($totalSales, 2, ',', '.'); ?> ₺</span>
			</div>
		</div>

		<!-- Total Collections Card -->
		<div class="stat-card card-hover animate-slideInUp" style="animation-delay: 0.3s">
			<div class="stat-icon" style="background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);">
				<i class="bi bi-cash-coin"></i>
			</div>
			<div class="stat-info">
				<span class="stat-label">Toplam Tahsilat</span>
				<span class="stat-value"><?php echo number_format($totalCollections, 2, ',', '.'); ?> ₺</span>
			</div>
		</div>

		<!-- Total Receivables Card -->
		<div class="stat-card card-hover animate-slideInUp" style="animation-delay: 0.4s">
			<div class="stat-icon" style="background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);">
				<i class="bi bi-clipboard2-data-fill"></i>
			</div>
			<div class="stat-info">
				<span class="stat-label">Toplam Alacak</span>
				<span class="stat-value"><?php echo number_format($totalReceivables, 2, ',', '.'); ?> ₺</span>
			</div>
		</div>
	</div>

	<!-- Dashboard Content -->
	<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
		<!-- Recent Sales Table -->
		<div class="lg:col-span-2">
			<div class="card-hover animate-fadeIn" style="animation-delay: 0.5s">
				<div class="card-header">
					<div class="flex justify-between items-center">
						<h3 class="card-title">Son Satışlar</h3>
						<a href="transactions.php" class="btn btn-outline btn-sm">
							Tümünü Gör <i class="bi bi-chevron-right ml-1"></i>
						</a>
					</div>
				</div>
				<div class="p-0">
					<div class="table-container">
						<table class="table">
							<thead>
								<tr>
									<th>#</th>
									<th>Müşteri</th>
									<th>Tarih</th>
									<th>Tutar</th>
									<th>Not</th>
									<th>İşlem</th>
								</tr>
							</thead>
							<tbody>
								<?php
									$stmt = $pdo->query("SELECT t.id, t.customer_id, c.name AS customer_name, t.amount, t.note, t.created_at FROM transactions t JOIN customers c ON c.id = t.customer_id WHERE t.type='debit' ORDER BY t.created_at DESC LIMIT 10");
									$i = 0;
									foreach ($stmt as $row):
									$i++;
								?>
								<tr class="animate-fadeIn" style="animation-delay: <?php echo 0.5 + ($i * 0.05); ?>s">
									<td><?php echo $row['id']; ?></td>
									<td>
										<a href="customer_report.php?customer=<?php echo (int)$row['customer_id']; ?>" class="text-primary-600 hover:text-primary-900 transition-colors duration-200">
											<?php echo htmlspecialchars($row['customer_name']); ?>
										</a>
									</td>
									<td>
										<?php echo date('d.m.Y H:i', strtotime($row['created_at'])); ?>
									</td>
									<td class="font-medium">
										<?php echo number_format($row['amount'], 2, ',', '.'); ?> ₺
									</td>
									<td>
										<?php echo htmlspecialchars($row['note']); ?>
									</td>
									<td>
										<a href="print.php?id=<?php echo $row['id']; ?>" class="text-gray-600 hover:text-gray-900 transition-colors duration-200" data-tooltip="Yazdır">
											<i class="bi bi-printer"></i>
										</a>
									</td>
								</tr>
								<?php endforeach; ?>
								<?php if ($i === 0): ?>
								<tr>
									<td colspan="6" class="text-center py-8 text-gray-500">
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
			<div class="card-hover animate-fadeIn" style="animation-delay: 0.6s">
				<div class="card-header">
					<div class="flex justify-between items-center">
						<h3 class="card-title">Borçlu Müşteriler</h3>
						<a href="customers.php" class="btn btn-outline btn-sm">
							Tümünü Gör <i class="bi bi-chevron-right ml-1"></i>
						</a>
					</div>
				</div>
				<div class="p-4">
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
							<span class="badge-danger">
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
			<div class="card-hover mt-6 animate-fadeIn" style="animation-delay: 0.7s">
				<div class="card-header">
					<h3 class="card-title">Hızlı Erişim</h3>
				</div>
				<div class="p-4">
					<div class="grid grid-cols-2 gap-4">
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
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>