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

<div class="floating-element"></div>
<div class="floating-element"></div>
<div class="floating-element"></div>


<div class="container mx-auto px-4 py-6">
	<h1 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
		<i class="bi bi-speedometer2 mr-2 text-primary-600"></i> Yönetim Paneli
	</h1>
	
	<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 dashboard-stats">
		<div class="stat-card card-hover animate-slideInUp" style="animation-delay: 0.1s">
			<div class="stat-icon">
				<i class="bi bi-people-fill"></i>
			</div>
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
				<span class="stat-value"><?php echo number_format($totalSales, 2, ',', '.'); ?> ₺</span>
			</div>
		</div>

		<div class="stat-card card-hover animate-slideInUp" style="animation-delay: 0.3s">
			<div class="stat-icon" style="background: linear-gradient(135deg, #16a34a 0%, #15803d 100%);">
				<i class="bi bi-cash-coin"></i>
			</div>
			<div class="stat-info">
				<span class="stat-label">Toplam Tahsilat</span>
				<span class="stat-value"><?php echo number_format($totalCollections, 2, ',', '.'); ?> ₺</span>
			</div>
		</div>

		<div class="stat-card card-hover animate-slideInUp" style="animation-delay: 0.4s">
			<div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
				<i class="bi bi-wallet2"></i>
			</div>
			<div class="stat-info">
				<span class="stat-label">Toplam Alacak</span>
				<span class="stat-value"><?php echo number_format($totalReceivables, 2, ',', '.'); ?> ₺</span>
			</div>
		</div>
	</div>

	<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
		<div class="lg:col-span-2">
			<div class="card-hover animate-fadeIn" style="animation-delay: 0.5s">
				<div class="card-header">
					<div class="flex justify-between items-center">
						<h3 class="card-title"><i class="bi bi-receipt mr-2"></i>Son Satışlar</h3>
						<a href="transactions.php" class="btn btn-outline btn-sm">
							Tümünü Gör <i class="bi bi-chevron-right ml-1"></i>
						</a>
					</div>
				</div>
				<div class="p-0">
					<div class="table-container">
						<table class="table table-hover">
							<thead>
								<tr>
									<th>#</th>
									<th>Müşteri</th>
									<th>Tarih</th>
									<th>Tutar</th>
									<th>Not</th>
									<th class="text-center">İşlem</th>
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
										<span class="text-gray-600"><i class="bi bi-calendar3 mr-1"></i><?php echo date('d.m.Y H:i', strtotime($row['created_at'])); ?></span>
									</td>
									<td class="font-medium text-primary-700">
										<?php echo number_format($row['amount'], 2, ',', '.'); ?> ₺
									</td>
									<td>
										<?php echo htmlspecialchars($row['note']); ?>
									</td>
									<td class="text-center">
										<a href="print.php?id=<?php echo $row['id']; ?>" class="btn btn-icon btn-sm btn-ghost" data-tooltip="Yazdır">
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

		<div class="lg:col-span-1">
			<div class="card-hover animate-fadeIn" style="animation-delay: 0.6s">
				<div class="card-header">
					<div class="flex justify-between items-center">
						<h3 class="card-title"><i class="bi bi-exclamation-triangle mr-2"></i>Borçlu Müşteriler</h3>
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
						<div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-0 animate-fadeIn hover:bg-gray-50 px-2 rounded transition-colors duration-200" style="animation-delay: <?php echo 0.6 + ($i * 0.05); ?>s">
							<a href="customer_report.php?customer=<?php echo $debtor['id']; ?>" class="text-gray-800 hover:text-primary-600 font-medium transition-colors duration-200">
								<i class="bi bi-person mr-1"></i> <?php echo htmlspecialchars($debtor['name']); ?>
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
		</div>
	</div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>