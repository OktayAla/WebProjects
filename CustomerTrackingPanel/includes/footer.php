<?php
r class="bg-white border-gray-200 border-t mt-auto py-8"><div class="lg:px-8 max-w-7xl mx-auto px-4 sm:px-6"><div class="flex items-center flex-col justify-between md:flex-row"><div class="flex items-center mb-4 md:mb-0"></div><div class="flex items-center flex-col md:items-end"><p class="md:text-right text-center text-gray-500 text-sm">Copyright © Tüm hakları saklıdır.</p><p class="md:text-right text-center mt-1 text-gray-400 text-xs">OA Grafik Tasarım tarafından <span class="text-danger-600">❤️</span> ile geliştirilmiştir.</p></div></div></div></footer><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script><script src="assets/js/main.js"></script><script>(function(){
			const menuButton = document.getElementById('mobile-menu-button');
			const menu = document.getElementById('mobile-menu');
			if (menuButton && menu) {
				const toggleMenu = function(e){
					if (e) e.preventDefault();
					const willOpen = menu.classList.contains('hidden');
					menu.classList.toggle('hidden');
					if (willOpen) {
						menu.style.display = 'block';
					} else {
						menu.style.display = '';
					}
					menuButton.setAttribute('aria-expanded', String(willOpen));
				};
				menuButton.addEventListener('click', toggleMenu, { passive: true });
				menuButton.addEventListener('touchstart', toggleMenu, { passive: true });
				// Dışarı tıklayınca kapat
				document.addEventListener('click', function(event) {
					if (!menu.classList.contains('hidden') && !menu.contains(event.target) && !menuButton.contains(event.target)) {
						menu.classList.add('hidden');
						menu.style.display = '';
						menuButton.setAttribute('aria-expanded', 'false');
					}
				});
			}
		})();
		
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
		});</script>