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
                const isChecked = toggle.checked;
                localStorage.setItem(key, isChecked ? '1' : '0');
                
                // Show notification
                showNotification(isChecked ? 
                    'Otomatik yenileme açıldı (30sn)' : 
                    'Otomatik yenileme kapatıldı', 
                    isChecked ? 'success' : 'info'
                );
                
                if (isChecked && (window.location.pathname.endsWith('index.php') || 
                    window.location.pathname === '/')) {
                    location.reload();
                }
            });
        }
    };
    
    setupToggle(desktopToggle);
    setupToggle(mobileToggle);
    
    // Set up auto refresh if enabled and on dashboard
    let refreshInterval;
    if (isEnabled && (window.location.pathname.endsWith('index.php') || 
        window.location.pathname === '/')) {
        refreshInterval = setInterval(function() {
            location.reload();
        }, 30000);
    }
    
    // Add active class to current page in navigation
    const currentPath = window.location.pathname;
    const filename = currentPath.substring(currentPath.lastIndexOf('/') + 1);
    
    const navLinks = document.querySelectorAll('.navbar-item');
    navLinks.forEach(link => {
        const linkHref = link.getAttribute('href');
        if (linkHref === filename || 
            (filename === '' && linkHref === 'index.php') || 
            (filename === 'index.php' && linkHref === 'index.php')) {
            link.classList.add('text-white', 'rounded-md');
            link.classList.remove('hover:bg-primary-700');
        }
    });
    
    // Enhanced tooltip system
    const initializeTooltips = function() {
        const tooltipTriggers = document.querySelectorAll('[data-tooltip]');
        
        tooltipTriggers.forEach(trigger => {
            // Remove existing event listeners to prevent duplicates
            trigger._tooltipHandlers = trigger._tooltipHandlers || {};
            
            if (trigger._tooltipHandlers.mouseenter) {
                trigger.removeEventListener('mouseenter', trigger._tooltipHandlers.mouseenter);
                trigger.removeEventListener('mouseleave', trigger._tooltipHandlers.mouseleave);
                trigger.removeEventListener('focus', trigger._tooltipHandlers.focus);
                trigger.removeEventListener('blur', trigger._tooltipHandlers.blur);
            }
            
            const showTooltip = function(event) {
                // Remove any existing tooltip
                const existingTooltip = document.querySelector('.custom-tooltip');
                if (existingTooltip) {
                    existingTooltip.remove();
                }
                
                const tooltipText = this.getAttribute('data-tooltip');
                if (!tooltipText) return;
                
                const tooltip = document.createElement('div');
                tooltip.className = 'custom-tooltip absolute z-50 px-3 py-2 text-sm text-white bg-gray-900 rounded-lg shadow-lg opacity-0 transform transition-all duration-200 scale-95';
                tooltip.textContent = tooltipText;
                
                document.body.appendChild(tooltip);
                
                // Position tooltip
                const rect = this.getBoundingClientRect();
                const tooltipHeight = tooltip.offsetHeight;
                const tooltipWidth = tooltip.offsetWidth;
                
                // Position above element by default
                let top = rect.top - tooltipHeight - 8;
                let left = rect.left + (rect.width / 2) - (tooltipWidth / 2);
                
                // Adjust if tooltip would go off-screen
                if (top < 10) {
                    top = rect.bottom + 8;
                }
                if (left < 10) {
                    left = 10;
                }
                if (left + tooltipWidth > window.innerWidth - 10) {
                    left = window.innerWidth - tooltipWidth - 10;
                }
                
                tooltip.style.top = `${top + window.scrollY}px`;
                tooltip.style.left = `${left}px`;
                
                // Animate in
                requestAnimationFrame(() => {
                    tooltip.classList.remove('opacity-0', 'scale-95');
                    tooltip.classList.add('opacity-100', 'scale-100');
                });
            };
            
            const hideTooltip = function() {
                const tooltip = document.querySelector('.custom-tooltip');
                if (tooltip) {
                    tooltip.classList.remove('opacity-100', 'scale-100');
                    tooltip.classList.add('opacity-0', 'scale-95');
                    
                    setTimeout(() => {
                        if (tooltip && tooltip.parentNode) {
                            tooltip.parentNode.removeChild(tooltip);
                        }
                    }, 200);
                }
            };
            
            // Store handlers for cleanup
            trigger._tooltipHandlers.mouseenter = showTooltip;
            trigger._tooltipHandlers.mouseleave = hideTooltip;
            trigger._tooltipHandlers.focus = showTooltip;
            trigger._tooltipHandlers.blur = hideTooltip;
            
            trigger.addEventListener('mouseenter', showTooltip);
            trigger.addEventListener('mouseleave', hideTooltip);
            trigger.addEventListener('focus', showTooltip);
            trigger.addEventListener('blur', hideTooltip);
        });
    };
    
    initializeTooltips();
    
    // Form enhancements
    const enhanceForms = function() {
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const submitBtn = this.querySelector('button[type="submit"], input[type="submit"]');
                if (submitBtn && !submitBtn.disabled) {
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin mr-2"></i> İşleniyor...';
                    submitBtn.disabled = true;
                    
                    // Revert after 10 seconds if still disabled (form didn't submit)
                    setTimeout(() => {
                        if (submitBtn.disabled) {
                            submitBtn.innerHTML = originalText;
                            submitBtn.disabled = false;
                        }
                    }, 10000);
                }
            });
        });
    };
    
    enhanceForms();
    
    // Notification system
    window.showNotification = function(message, type = 'info', duration = 3000) {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full opacity-0`;
        
        let bgColor = 'bg-primary-600';
        if (type === 'success') bgColor = 'bg-success-600';
        if (type === 'danger') bgColor = 'bg-danger-600';
        if (type === 'warning') bgColor = 'bg-warning-600';
        
        notification.className += ` ${bgColor} text-white`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Animate in
        requestAnimationFrame(() => {
            notification.classList.remove('translate-x-full', 'opacity-0');
            notification.classList.add('translate-x-0', 'opacity-100');
        });
        
        // Auto remove after duration
        setTimeout(() => {
            notification.classList.remove('translate-x-0', 'opacity-100');
            notification.classList.add('translate-x-full', 'opacity-0');
            
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, duration);
        
        // Click to dismiss
        notification.addEventListener('click', () => {
            notification.classList.remove('translate-x-0', 'opacity-100');
            notification.classList.add('translate-x-full', 'opacity-0');
            
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        });
    };
    
    const enhanceTables = function() {
        document.querySelectorAll('input[type="search"]').forEach(input => {
            const tableId = input.getAttribute('data-table');
            if (!tableId) return;
            
            const table = document.getElementById(tableId);
            if (!table) return;
            
            input.addEventListener('input', function() {
                const searchText = this.value.toLowerCase();
                const rows = table.querySelectorAll('tbody tr');
                
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchText) ? '' : 'none';
                });
            });
        });
    };
    
    enhanceTables();
    
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href === '#') return;
            
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    const enhanceCards = function() {
        document.querySelectorAll('.card-hover').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
                this.style.boxShadow = '0 20px 40px rgba(0, 0, 0, 0.15)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = '';
                this.style.boxShadow = '';
            });
        });
    };
    
    enhanceCards();
    
    window.enhancedPrint = function(elementId = null) {
        const originalStyles = document.querySelectorAll('style, link[rel="stylesheet"]');
        originalStyles.forEach(style => style.setAttribute('media', 'screen'));
        
        const printStyle = document.createElement('style');
        printStyle.textContent = `
            @media print {
                body * {
                    visibility: hidden;
                }
                ${elementId ? `#${elementId}, #${elementId} *` : '.print-area, .print-area *'} {
                    visibility: visible;
                }
                ${elementId ? `#${elementId}` : '.print-area'} {
                    position: absolute;
                    left: 0;
                    top: 0;
                    width: 100%;
                }
                .no-print, .navbar, footer, .btn {
                    display: none !important;
                }
            }
        `;
        document.head.appendChild(printStyle);
        
        window.print();
        
        setTimeout(() => {
            document.head.removeChild(printStyle);
            originalStyles.forEach(style => style.setAttribute('media', 'all'));
        }, 100);
    };
    
    console.log('OA Grafik Tasarım - Müşteri Takip Sistemi yüklendi');
});

window.debounce = function(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
};

window.formatCurrency = function(amount) {
    return new Intl.NumberFormat('tr-TR', {
        style: 'currency',
        currency: 'TRY'
    }).format(amount);
};

if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        debounce: window.debounce,
        formatCurrency: window.formatCurrency
    };
}