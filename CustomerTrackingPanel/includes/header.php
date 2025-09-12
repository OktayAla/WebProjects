<?php require_once __DIR__ . '/auth.php'; ?>
<!DOCTYPE html>
<html lang="tr">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Müşteri Portalı</title>
	<script src="https://cdn.tailwindcss.com"></script>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
	<link href="assets/css/style.css" rel="stylesheet">
	<script>
		tailwind.config = {
			theme: {
				extend: {
					fontFamily: {
						sans: ['Inter', 'sans-serif'],
					},
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
						secondary: {
							50: '#f1f5f9',
							100: '#e2e8f0',
							200: '#cbd5e1',
							300: '#94a3b8',
							400: '#64748b',
							500: '#475569',
							600: '#334155',
							700: '#1f2937',
							800: '#0f172a',
							900: '#0b1220',
							950: '#060a12',
						},
						success: {
							50: '#f0fdf4',
							100: '#dcfce7',
							200: '#bbf7d0',
							300: '#86efac',
							400: '#4ade80',
							500: '#22c55e',
							600: '#16a34a',
							700: '#15803d',
							800: '#166534',
							900: '#14532d',
							950: '#052e16',
						},
						danger: {
							50: '#fef2f2',
							100: '#fee2e2',
							200: '#fecaca',
							300: '#fca5a5',
							400: '#f87171',
							500: '#ef4444',
							600: '#dc2626',
							700: '#b91c1c',
							800: '#991b1b',
							900: '#7f1d1d',
							950: '#450a0a',
						},
					},
				},
			},
			plugins: [],
		}
	</script>
</head>
<body class="bg-slate-50 min-h-screen flex flex-col">
	
	<nav class="navbar-gradient shadow-lg z-10">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
			<div class="flex justify-between h-16">
				<div class="flex">
					<div class="flex-shrink-0 flex items-center">
						<a href="index.php" class="text-white text-xl font-bold flex items-center">
							Müşteri Portalı
						</a>
					</div>
					<div class="hidden md:ml-6 md:flex md:space-x-1">
						<a href="index.php" class="navbar-item text-white px-3 py-2 text-sm font-medium flex items-center">
							<i class="bi bi-speedometer2 mr-2"></i> Panel
						</a>
						<a href="musteriler.php" class="navbar-item text-white px-3 py-2 text-sm font-medium flex items-center">
							<i class="bi bi-people mr-2"></i> Müşteriler
						</a>
						<a href="islemler.php" class="navbar-item text-white px-3 py-2 text-sm font-medium flex items-center">
							<i class="bi bi-cash-stack mr-2"></i> İşlemler
						</a>
						<a href="urunler.php" class="navbar-item text-white px-3 py-2 text-sm font-medium flex items-center">
							<i class="bi bi-box mr-2"></i> Ürünler
						</a>
						<a href="fiyatlar.php" class="navbar-item text-white px-3 py-2 text-sm font-medium flex items-center">
							<i class="bi bi-currency-dollar mr-2"></i> Fiyatlar
						</a>
						<?php if (current_user() && current_user()['rol'] === 'admin'): ?>
						<a href="kullanicilar.php" class="navbar-item text-white px-3 py-2 text-sm font-medium flex items-center">
							<i class="bi bi-people-fill mr-2"></i> Kullanıcılar
						</a>
						<?php endif; ?>
					</div>
				</div>
				<div class="hidden md:flex items-center">
					<?php if (current_user()): ?>
					<div class="ml-3 relative">
						<div>
							<button type="button" id="user-menu-button" class="flex items-center max-w-xs text-sm rounded-full text-white focus:outline-none">
								<span class="mr-2"><?php echo htmlspecialchars(current_user()['isim']); ?></span>
								<i class="bi bi-person-circle text-xl"></i>
							</button>
						</div>
						<div id="user-dropdown" class="hidden absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 z-50">
							<a href="cikis.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-200">
								<i class="bi bi-box-arrow-right mr-2"></i> Çıkış
							</a>
						</div>
					</div>
					
					<?php endif; ?>
				</div>
				<div class="-mr-2 flex md:hidden">
					<button type="button" id="mobile-menu-button" class="inline-flex items-center justify-center p-2 rounded-md text-white hover:bg-primary-700 focus:outline-none transition-colors duration-200">
						<i class="bi bi-list text-2xl"></i>
					</button>
				</div>
			</div>
		</div>
		
		<div class="hidden md:hidden" id="mobile-menu">
			<div class="px-2 pt-2 pb-3 space-y-1">
				<a href="index.php" class="navbar-item text-white block px-3 py-2 rounded-md text-base font-medium">
					<i class="bi bi-speedometer2 mr-2"></i> Panel
				</a>
				<a href="musteriler.php" class="navbar-item text-white block px-3 py-2 rounded-md text-base font-medium">
					<i class="bi bi-people mr-2"></i> Müşteriler
				</a>
				<a href="islemler.php" class="navbar-item text-white block px-3 py-2 rounded-md text-base font-medium">
					<i class="bi bi-cash-stack mr-2"></i> İşlemler
				</a>
				<a href="urunler.php" class="navbar-item text-white block px-3 py-2 rounded-md text-base font-medium">
					<i class="bi bi-box mr-2"></i> Ürünler
				</a>
				<a href="fiyatlar.php" class="navbar-item text-white block px-3 py-2 rounded-md text-base font-medium">
					<i class="bi bi-currency-dollar mr-2"></i> Fiyatlar
				</a>
				<?php if (current_user() && current_user()['rol'] === 'admin'): ?>
				<a href="kullanicilar.php" class="navbar-item text-white block px-3 py-2 rounded-md text-base font-medium">
					<i class="bi bi-people-fill mr-2"></i> Kullanıcılar
				</a>
				<?php endif; ?>
			</div>
			<div class="pt-4 pb-3 border-t border-gray-600">
				<div class="flex items-center px-5">
					<div class="flex-shrink-0">
						<i class="bi bi-person-circle text-xl text-white"></i>
					</div>
					<div class="ml-3">
						<div class="text-base font-medium text-white"><?php echo htmlspecialchars(current_user()['isim']); ?></div>
					</div>
				</div>
				<div class="mt-3 px-2 space-y-1">
					<a href="cikis.php" class="block px-3 py-2 rounded-md text-base font-medium text-white hover:bg-primary-700 transition-colors duration-200">
						<i class="bi bi-box-arrow-right mr-2"></i> Çıkış
					</a>
				</div>
			</div>
		</div>
	</nav>
	
	<script>
		document.addEventListener('DOMContentLoaded', function() {
			const userMenuButton = document.getElementById('user-menu-button');
			const userDropdown = document.getElementById('user-dropdown');
			
			// Kullanıcı menüsü (mobil menü footer'da yönetiliyor)
			if (userMenuButton && userDropdown) {
				userMenuButton.addEventListener('click', function() {
					userDropdown.classList.toggle('hidden');
				});
				// Dropdown dışına tıklanınca kapat
				document.addEventListener('click', function(event) {
					if (!userMenuButton.contains(event.target) && !userDropdown.contains(event.target)) {
						userDropdown.classList.add('hidden');
					}
				});
			}
		});
	</script>
	
	<main class="flex-grow">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">