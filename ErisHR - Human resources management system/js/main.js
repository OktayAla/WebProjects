/**
 * HRMS - İnsan Kaynakları Yönetim Sistemi
 * Main JavaScript File
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize sidebar toggle functionality
    initSidebar();
    
    // Initialize date and time display
    initDateTime();
    
    // Initialize notifications
    initNotifications();
});

/**
 * Initialize sidebar toggle functionality
 */
function initSidebar() {
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');
    
    if (sidebarToggle && sidebar && mainContent) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
            
            // Store sidebar state in localStorage
            const isCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebar-collapsed', isCollapsed);
        });
        
        // Check if sidebar state is stored in localStorage
        const isCollapsed = localStorage.getItem('sidebar-collapsed') === 'true';
        if (isCollapsed) {
            sidebar.classList.add('collapsed');
            mainContent.classList.add('expanded');
        }
    }
}

/**
 * Initialize date and time display
 */
function initDateTime() {
    const currentDateElement = document.getElementById('current-date');
    const currentTimeElement = document.getElementById('current-time');
    
    if (currentDateElement && currentTimeElement) {
        // Update date and time
        updateDateTime(currentDateElement, currentTimeElement);
        
        // Update time every second
        setInterval(function() {
            updateDateTime(currentDateElement, currentTimeElement);
        }, 1000);
    }
}

/**
 * Update date and time elements
 */
function updateDateTime(dateElement, timeElement) {
    const now = new Date();
    
    // Format date: DD.MM.YYYY
    const day = String(now.getDate()).padStart(2, '0');
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const year = now.getFullYear();
    const dateString = `${day}.${month}.${year}`;
    
    // Format time: HH:MM:SS
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    const timeString = `${hours}:${minutes}:${seconds}`;
    
    // Update elements
    dateElement.textContent = dateString;
    timeElement.textContent = timeString;
}

/**
 * Initialize notifications
 */
function initNotifications() {
    const notificationIcon = document.querySelector('.notification');
    
    if (notificationIcon) {
        notificationIcon.addEventListener('click', function() {
            // Toggle notification panel (to be implemented)
            console.log('Notification panel toggled');
        });
    }
}

/**
 * Format date for display
 * @param {string} dateString - Date string in ISO format
 * @returns {string} Formatted date string
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    
    return `${day}.${month}.${year}`;
}

/**
 * Format time for display
 * @param {string} timeString - Time string in HH:MM:SS format
 * @returns {string} Formatted time string
 */
function formatTime(timeString) {
    return timeString.substring(0, 5); // Return only HH:MM
}

/**
 * Show a toast notification
 * @param {string} message - Message to display
 * @param {string} type - Type of notification (success, error, warning, info)
 */
function showToast(message, type = 'info') {
    // Create toast container if it doesn't exist
    let toastContainer = document.querySelector('.toast-container');
    
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container';
        document.body.appendChild(toastContainer);
    }
    
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast toast-${type} animate__animated animate__fadeInRight`;
    toast.innerHTML = `
        <div class="toast-icon">
            <i class="fas ${getToastIcon(type)}"></i>
        </div>
        <div class="toast-content">
            <p>${message}</p>
        </div>
        <button class="toast-close">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    // Add toast to container
    toastContainer.appendChild(toast);
    
    // Add event listener to close button
    const closeButton = toast.querySelector('.toast-close');
    closeButton.addEventListener('click', function() {
        toast.classList.replace('animate__fadeInRight', 'animate__fadeOutRight');
        setTimeout(() => {
            toast.remove();
        }, 500);
    });
    
    // Auto-remove toast after 5 seconds
    setTimeout(() => {
        if (toast.parentNode) {
            toast.classList.replace('animate__fadeInRight', 'animate__fadeOutRight');
            setTimeout(() => {
                toast.remove();
            }, 500);
        }
    }, 5000);
}

/**
 * Get icon class for toast notification
 * @param {string} type - Type of notification
 * @returns {string} Icon class
 */
function getToastIcon(type) {
    switch (type) {
        case 'success':
            return 'fa-check-circle';
        case 'error':
            return 'fa-exclamation-circle';
        case 'warning':
            return 'fa-exclamation-triangle';
        case 'info':
        default:
            return 'fa-info-circle';
    }
}

/**
 * PDKS Card Reader Simulation
 * @param {string} cardId - RFID Card ID
 */
function simulatePDKSCardRead(cardId) {
    // Get current date and time
    const now = new Date();
    const dateString = now.toISOString().split('T')[0];
    const timeString = now.toTimeString().substring(0, 8);
    
    // Create PDKS record
    const pdksRecord = {
        cardId: cardId,
        timestamp: now.toISOString(),
        date: dateString,
        time: timeString
    };
    
    // Send PDKS record to server
    fetch('api/pdks_record.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(pdksRecord)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success notification
            showToast(`${data.employeeName} için ${data.type} kaydı oluşturuldu.`, 'success');
            
            // Update UI if needed
            if (typeof updatePDKSHistory === 'function') {
                updatePDKSHistory();
            }
        } else {
            // Show error notification
            showToast(data.message || 'Kart okuma işlemi başarısız.', 'error');
        }
    })
    .catch(error => {
        console.error('PDKS card read error:', error);
        showToast('Kart okuma işlemi sırasında bir hata oluştu.', 'error');
    });
}

/**
 * Load user data from JSON file
 * @param {number} userId - User ID
 * @returns {Promise} Promise that resolves with user data
 */
function loadUserData(userId) {
    return fetch(`js/users/${userId}.js`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Kullanıcı verisi bulunamadı.');
            }
            return response.json();
        })
        .catch(error => {
            console.error('Error loading user data:', error);
            showToast('Kullanıcı verisi yüklenirken bir hata oluştu.', 'error');
            return null;
        });
}

/**
 * Format currency for display
 * @param {number} amount - Amount to format
 * @returns {string} Formatted currency string
 */
function formatCurrency(amount) {
    return new Intl.NumberFormat('tr-TR', {
        style: 'currency',
        currency: 'TRY',
        minimumFractionDigits: 2
    }).format(amount);
}

/**
 * Calculate date difference in days
 * @param {string} startDate - Start date in ISO format
 * @param {string} endDate - End date in ISO format
 * @returns {number} Number of days
 */
function calculateDateDiff(startDate, endDate) {
    const start = new Date(startDate);
    const end = new Date(endDate);
    const diffTime = Math.abs(end - start);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    return diffDays + 1; // Include both start and end days
}

/**
 * Check if a date is a weekend (Saturday or Sunday)
 * @param {string} dateString - Date string in ISO format
 * @returns {boolean} True if weekend, false otherwise
 */
function isWeekend(dateString) {
    const date = new Date(dateString);
    const day = date.getDay();
    
    return day === 0 || day === 6; // 0 = Sunday, 6 = Saturday
}

/**
 * Generate a random ID
 * @returns {string} Random ID
 */
function generateId() {
    return Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
}