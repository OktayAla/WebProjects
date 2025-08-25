</main>
	<footer class="bg-white py-4 mt-8 border-t border-gray-200">
		<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
			<p class="text-center text-gray-500 text-sm">OA Grafik Tasarım tarafından ❤️ ile geliştirilmiştir. - Tüm hakları saklıdır.</p>
		</div>
	</footer>
	<script src="assets/js/main.js"></script>
	<script>
		// Mobile menu toggle
		document.getElementById('mobile-menu-button').addEventListener('click', function() {
			const menu = document.getElementById('mobile-menu');
			menu.classList.toggle('hidden');
		});
		
		// Sync mobile toggle with desktop toggle
		const desktopToggle = document.getElementById('autoRefreshToggle');
		const mobileToggle = document.getElementById('autoRefreshToggleMobile');
		
		if (desktopToggle && mobileToggle) {
			desktopToggle.addEventListener('change', function() {
				mobileToggle.checked = desktopToggle.checked;
			});
			
			mobileToggle.addEventListener('change', function() {
				desktopToggle.checked = mobileToggle.checked;
			});
		}
	</script>
</body>
</html>


