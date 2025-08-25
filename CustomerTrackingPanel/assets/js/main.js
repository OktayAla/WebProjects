document.addEventListener('DOMContentLoaded', function() {
	// Auto refresh functionality
	const desktopToggle = document.getElementById('autoRefreshToggle');
	const mobileToggle = document.getElementById('autoRefreshToggleMobile');
	const key = 'auto_refresh_enabled';
	
	// Initialize toggles based on localStorage
	const isEnabled = localStorage.getItem(key) === '1';
	if (desktopToggle) {
		desktopToggle.checked = isEnabled;
	}
	if (mobileToggle) {
		mobileToggle.checked = isEnabled;
	}
	
	// Setup toggle event listeners
	const setupToggle = function(toggle) {
		if (toggle) {
			toggle.addEventListener('change', function() {
				localStorage.setItem(key, toggle.checked ? '1' : '0');
				if (toggle.checked) location.reload();
			});
		}
	};
	
	setupToggle(desktopToggle);
	setupToggle(mobileToggle);
	
	// Set up auto refresh if enabled
	if (isEnabled) {
		setInterval(function() {
			location.reload();
		}, 30000);
	}
	
	// Add active class to current page in navigation
	const currentPath = window.location.pathname;
	const filename = currentPath.substring(currentPath.lastIndexOf('/') + 1);
	
	const navLinks = document.querySelectorAll('nav a');
	navLinks.forEach(link => {
		const linkHref = link.getAttribute('href');
		if (linkHref === filename || 
			(filename === '' && linkHref === 'index.php') || 
			(filename === 'index.php' && linkHref === 'index.php')) {
			link.classList.add('bg-primary-700');
		}
	});
	
	// Initialize tooltips
	const tooltipTriggers = document.querySelectorAll('[data-tooltip]');
	tooltipTriggers.forEach(trigger => {
		trigger.addEventListener('mouseenter', function() {
			const tooltip = document.createElement('div');
			tooltip.className = 'absolute z-50 px-2 py-1 text-xs text-white bg-gray-900 rounded shadow-lg';
			tooltip.textContent = this.getAttribute('data-tooltip');
			tooltip.style.bottom = '100%';
			tooltip.style.left = '50%';
			tooltip.style.transform = 'translateX(-50%) translateY(-5px)';
			this.style.position = 'relative';
			this.appendChild(tooltip);
			
			this.addEventListener('mouseleave', function() {
				if (tooltip && tooltip.parentNode) {
					tooltip.parentNode.removeChild(tooltip);
				}
			}, { once: true });
		});
	});
});


