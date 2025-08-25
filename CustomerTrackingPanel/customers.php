<?php require_once __DIR__ . '/includes/auth.php'; require_login(); require_once __DIR__ . '/includes/header.php'; ?>
<?php
	$pdo = get_pdo_connection();

	// Create / Update customer
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
		$name = trim($_POST['name']);
		$phone = trim($_POST['phone']);
		$address = trim($_POST['address']);
		if ($id > 0) {
			$stmt = $pdo->prepare('UPDATE customers SET name = ?, phone = ?, address = ? WHERE id = ?');
			$stmt->execute([$name, $phone, $address, $id]);
		} else {
			$stmt = $pdo->prepare('INSERT INTO customers (name, phone, address) VALUES (?, ?, ?)');
			$stmt->execute([$name, $phone, $address]);
		}
		header('Location: customers.php');
		exit;
	}

	// Delete customer
	if (isset($_GET['delete'])) {
		$delId = (int)$_GET['delete'];
		$pdo->prepare('DELETE FROM transactions WHERE customer_id = ?')->execute([$delId]);
		$pdo->prepare('DELETE FROM customers WHERE id = ?')->execute([$delId]);
		header('Location: customers.php');
		exit;
	}

	$editCustomer = null;
	if (isset($_GET['edit'])) {
		$stmt = $pdo->prepare('SELECT * FROM customers WHERE id = ?');
		$stmt->execute([(int)$_GET['edit']]);
		$editCustomer = $stmt->fetch();
	}
?>

	<!-- Breadcrumb -->
	<div class="flex items-center space-x-2 text-sm text-gray-500 mb-6">
		<a href="index.php" class="hover:text-primary-600 transition-colors duration-200">Panel</a>
		<span class="text-gray-400">/</span>
		<span class="text-gray-700 font-medium">Müşteriler</span>
	</div>

	<!-- Header -->
	<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
		<h1 class="text-2xl font-bold text-gray-900">Müşteri Yönetimi</h1>
		<button id="newCustomerBtn" class="btn btn-primary flex items-center justify-center" data-bs-toggle="modal" data-bs-target="#customerModal">
			<i class="bi bi-person-plus mr-2"></i> Yeni Müşteri
		</button>
	</div>

	<!-- Customer List Card -->
	<div class="bg-white rounded-xl shadow-sm overflow-hidden transition-all duration-300 hover:shadow-md">
		<div class="p-6">
			<!-- Search Bar -->
			<div class="flex flex-col md:flex-row gap-3 mb-6">
				<div class="relative flex-grow">
					<div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
						<i class="bi bi-search text-gray-400"></i>
					</div>
					<input type="text" id="searchInput" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm" placeholder="Müşteri ara (ad/telefon)">
				</div>
				<button id="clearSearch" class="btn btn-outline inline-flex items-center px-4 py-2">
					<i class="bi bi-x-lg mr-2"></i> Temizle
				</button>
			</div>

			<!-- Table -->
			<div class="overflow-x-auto">
				<table id="customersTable" class="min-w-full divide-y divide-gray-200">
					<thead class="bg-gray-50">
						<tr>
							<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
							<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ad Soyad</th>
							<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Telefon</th>
							<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Adres</th>
							<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bakiye (₺)</th>
							<th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">İşlemler</th>
						</tr>
					</thead>
					<tbody class="bg-white divide-y divide-gray-200">
						<?php
							foreach ($pdo->query('SELECT * FROM customers ORDER BY id DESC') as $row):
						?>
						<tr class="hover:bg-gray-50 transition-colors duration-150">
							<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $row['id']; ?></td>
							<td class="px-6 py-4 whitespace-nowrap">
								<a href="customer_report.php?customer=<?php echo $row['id']; ?>" class="text-primary-600 hover:text-primary-900 font-medium">
									<?php echo htmlspecialchars($row['name']); ?>
								</a>
							</td>
							<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($row['phone']); ?></td>
							<td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate"><?php echo nl2br(htmlspecialchars($row['address'])); ?></td>
							<td class="px-6 py-4 whitespace-nowrap text-sm font-medium <?php echo $row['balance'] > 0 ? 'text-danger-600' : 'text-success-600'; ?>">
								<?php echo number_format($row['balance'], 2, ',', '.'); ?>
							</td>
							<td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
								<div class="flex space-x-2 justify-end">
									<a href="transactions.php?customer=<?php echo $row['id']; ?>" class="text-primary-600 hover:text-primary-900" title="İşlem Ekle">
										<i class="bi bi-cash-coin"></i>
									</a>
									<a href="customers.php?edit=<?php echo $row['id']; ?>" class="text-gray-600 hover:text-gray-900" title="Düzenle">
										<i class="bi bi-pencil"></i>
									</a>
									<a href="customers.php?delete=<?php echo $row['id']; ?>" class="text-danger-600 hover:text-danger-900" onclick="return confirm('Silmek istediğinize emin misiniz?');" title="Sil">
										<i class="bi bi-trash"></i>
									</a>
								</div>
							</td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<!-- Customer Modal -->
	<div class="modal fade" id="customerModal" tabindex="-1" aria-labelledby="customerModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content rounded-lg shadow-xl border-0">
				<form method="post" class="w-full">
					<div class="modal-header border-b border-gray-200 py-3 px-4 flex items-center justify-between">
						<h5 class="modal-title text-lg font-medium text-gray-900" id="customerModalLabel">
							<?php echo $editCustomer ? 'Müşteriyi Düzenle' : 'Yeni Müşteri'; ?>
						</h5>
						<button type="button" class="text-gray-400 hover:text-gray-500 focus:outline-none" data-bs-dismiss="modal" aria-label="Close">
							<i class="bi bi-x-lg"></i>
						</button>
					</div>
					<div class="modal-body p-6 space-y-4">
						<input type="hidden" name="id" value="<?php echo $editCustomer ? (int)$editCustomer['id'] : 0; ?>">
						
						<div>
							<label for="customerName" class="block text-sm font-medium text-gray-700 mb-1">Ad Soyad</label>
							<input type="text" name="name" id="customerName" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm" value="<?php echo $editCustomer ? htmlspecialchars($editCustomer['name']) : ''; ?>" required>
						</div>
						
						<div>
							<label for="customerPhone" class="block text-sm font-medium text-gray-700 mb-1">Telefon</label>
							<input type="text" name="phone" id="customerPhone" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm" value="<?php echo $editCustomer ? htmlspecialchars($editCustomer['phone']) : ''; ?>">
						</div>
						
						<div>
							<label for="customerAddress" class="block text-sm font-medium text-gray-700 mb-1">Adres</label>
							<textarea name="address" id="customerAddress" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm"><?php echo $editCustomer ? htmlspecialchars($editCustomer['address']) : ''; ?></textarea>
						</div>
					</div>
					
					<div class="modal-footer bg-gray-50 px-6 py-3 flex flex-row-reverse sm:px-6">
						<button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200">
							<?php echo $editCustomer ? 'Kaydet' : 'Ekle'; ?>
						</button>
						<button type="button" class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-colors duration-200" data-bs-dismiss="modal">
							Kapat
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>

<?php if ($editCustomer): ?>
<script>
	document.addEventListener('DOMContentLoaded', function() {
		var modal = new bootstrap.Modal(document.getElementById('customerModal'));
		modal.show();
	});
</script>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
	// Müşteri arama fonksiyonu
	const setupSearch = () => {
		const input = document.getElementById('searchInput');
		const clearBtn = document.getElementById('clearSearch');
		const table = document.getElementById('customersTable');
		const key = 'customers_search';
		
		if (!input || !table) return;
		
		// Kaydedilmiş filtreyi yükle
		input.value = localStorage.getItem(key) || '';
		
		// Filtreleme fonksiyonu
		function applyFilter() {
			const q = input.value.toLowerCase();
			const rows = table.tBodies[0].rows;
			let visibleCount = 0;
			
			for (const tr of rows) {
				const text = tr.innerText.toLowerCase();
				const isVisible = text.includes(q);
				tr.style.display = isVisible ? '' : 'none';
				tr.classList.toggle('animate-fadeIn', isVisible);
				if (isVisible) visibleCount++;
			}
			
			localStorage.setItem(key, q);
			
			// Sonuç sayısını göster
			const resultCount = document.getElementById('resultCount');
			if (resultCount) {
				resultCount.textContent = visibleCount + ' müşteri bulundu';
				resultCount.style.display = q ? 'block' : 'none';
			}
		}
		
		// Event listeners
		input.addEventListener('input', applyFilter);
		if (clearBtn) {
			clearBtn.addEventListener('click', function() { 
				input.value = ''; 
				applyFilter(); 
				input.focus();
			});
		}
		
		// İlk yüklemede filtreyi uygula
		applyFilter();
	};
	
	// Tooltip işlevselliği
	const setupTooltips = () => {
		const tooltips = document.querySelectorAll('[title]');
		tooltips.forEach(el => {
			el.setAttribute('data-tooltip', el.getAttribute('title'));
			el.removeAttribute('title');
		});
	};
	
	// Aktif sayfayı vurgula
	const highlightActivePage = () => {
		const currentPath = window.location.pathname;
		const navLinks = document.querySelectorAll('nav a');
		navLinks.forEach(link => {
			if (link.getAttribute('href') === currentPath || 
				(currentPath.includes('customers.php') && link.getAttribute('href').includes('customers.php'))) {
				link.classList.add('text-primary-600', 'font-medium');
			}
		});
	};
	
	// Tüm kurulumları çalıştır
	setupSearch();
	setupTooltips();
	highlightActivePage();
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>


