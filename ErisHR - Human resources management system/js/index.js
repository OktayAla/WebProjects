/**
 * HRMS - İnsan Kaynakları Yönetim Sistemi
 * Index Page JavaScript File
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize date and time display
    updateDateTime();
    
    // Initialize work hours calculation
    updateWorkHours();
});

/**
 * Update current date and time
 */
function updateDateTime() {
    const now = new Date();
    const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const timeOptions = { hour: '2-digit', minute: '2-digit', second: '2-digit' };
    
    document.getElementById('current-date').textContent = now.toLocaleDateString('tr-TR', dateOptions);
    document.getElementById('current-time').textContent = now.toLocaleTimeString('tr-TR', timeOptions);
    
    setTimeout(updateDateTime, 1000);
}

/**
 * Calculate and update work hours
 */
function updateWorkHours() {
    const now = new Date();
    const startTime = new Date();
    startTime.setHours(9, 0, 0); // Work starts at 9:00 AM
    
    if (now < startTime) {
        document.getElementById('workHours').textContent = '0:00';
        return;
    }
    
    const endTime = new Date();
    endTime.setHours(18, 0, 0); // Work ends at 6:00 PM
    
    const currentTime = now > endTime ? endTime : now;
    const diffMs = currentTime - startTime;
    const diffHrs = Math.floor(diffMs / 3600000);
    const diffMins = Math.floor((diffMs % 3600000) / 60000);
    
    document.getElementById('workHours').textContent = diffHrs + ':' + (diffMins < 10 ? '0' : '') + diffMins;
    
    setTimeout(updateWorkHours, 60000); // Update every minute
}