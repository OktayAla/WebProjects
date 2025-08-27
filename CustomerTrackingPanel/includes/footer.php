</main>
	
	<footer class="bg-white py-6 mt-8 border-t border-gray-200">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
			<div class="flex flex-col md:flex-row justify-between items-center">
				<p class="text-center md:text-left text-gray-500 text-sm mb-4 md:mb-0">
					OA Grafik Tasarım tarafından <span class="text-danger-600">❤️</span> ile geliştirilmiştir.
				</p>
				<div class="flex items-center space-x-4">
					<a href="#" class="text-gray-400 hover:text-primary-600 transition-colors duration-200">
						<i class="bi bi-question-circle text-lg"></i>
					</a>
					<a href="#" class="text-gray-400 hover:text-primary-600 transition-colors duration-200">
						<i class="bi bi-envelope text-lg"></i>
					</a>
					<a href="#" class="text-gray-400 hover:text-primary-600 transition-colors duration-200">
						<i class="bi bi-github text-lg"></i>
					</a>
				</div>
			</div>
		</div>
	</footer>
	
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
	<script src="assets/js/main.js"></script>
	<script>
		document.getElementById('mobile-menu-button').addEventListener('click', function() {
			const menu = document.getElementById('mobile-menu');
			menu.classList.toggle('hidden');
		});
		
		document.addEventListener('click', function(event) {
			const menu = document.getElementById('mobile-menu');
			const menuButton = document.getElementById('mobile-menu-button');
			
			if (!menu.classList.contains('hidden') && 
				!menu.contains(event.target) && 
				!menuButton.contains(event.target)) {
				menu.classList.add('hidden');
			}
		});
		
		const desktopToggle = document.getElementById('autoRefreshToggle');
		const mobileToggle = document.getElementById('autoRefreshToggleMobile');
		
		if (desktopToggle && mobileToggle) {
			desktopToggle.addEventListener('change', function() {
				mobileToggle.checked = desktopToggle.checked;
				localStorage.setItem('autoRefresh', desktopToggle.checked);
			});
			
			mobileToggle.addEventListener('change', function() {
				desktopToggle.checked = mobileToggle.checked;
				localStorage.setItem('autoRefresh', mobileToggle.checked);
			});
			
			document.addEventListener('DOMContentLoaded', function() {
				const autoRefresh = localStorage.getItem('autoRefresh') === 'true';
				if (desktopToggle) desktopToggle.checked = autoRefresh;
				if (mobileToggle) mobileToggle.checked = autoRefresh;
			});
		}
		
		function setupAutoRefresh() {
			const toggle = document.getElementById('autoRefreshToggle') || document.getElementById('autoRefreshToggleMobile');
			if (!toggle) return;
			
			let refreshInterval;
			
			function startAutoRefresh() {
				refreshInterval = setInterval(() => {
					if (window.location.pathname.endsWith('index.php') || 
						window.location.pathname.endsWith('/')) {
						window.location.reload();
					}
				}, 30000);
			}
			
			function stopAutoRefresh() {
				if (refreshInterval) {
					clearInterval(refreshInterval);
				}
			}
			
			toggle.addEventListener('change', function() {
				if (this.checked) {
					startAutoRefresh();
				} else {
					stopAutoRefresh();
				}
			});
			
			if (toggle.checked) {
				startAutoRefresh();
			}
		}
		
		document.addEventListener('DOMContentLoaded', function() {
			setupAutoRefresh();
			
			document.querySelectorAll('a[href^="#"]').forEach(anchor => {
				anchor.addEventListener('click', function (e) {
					e.preventDefault();
					const target = document.querySelector(this.getAttribute('href'));
					if (target) {
						target.scrollIntoView({
							behavior: 'smooth',
							block: 'start'
						});
					}
				});
			});
			
			document.querySelectorAll('form').forEach(form => {
				form.addEventListener('submit', function() {
					const submitBtn = this.querySelector('button[type="submit"]');
					if (submitBtn) {
						submitBtn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin mr-2"></i> İşleniyor...';
						submitBtn.disabled = true;
					}
				});
			});
		});
	</script>
</body>
</html>