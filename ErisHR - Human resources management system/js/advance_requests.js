/**
 * HRMS - İnsan Kaynakları Yönetim Sistemi
 * Advance Requests JavaScript File
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize advance requests functionality
    initAdvanceRequestsFilters();
    calculateAdvanceStatistics();
});

/**
 * Initialize advance requests filters
 */
function initAdvanceRequestsFilters() {
    // Filter panel toggle
    const filterButton = document.getElementById('filterButton');
    const filterPanel = document.querySelector('.filter-panel');
    
    if (filterButton && filterPanel) {
        filterButton.addEventListener('click', function() {
            filterPanel.style.display = filterPanel.style.display === 'none' ? 'block' : 'none';
        });
    }
    
    // Apply filter
    const applyFilter = document.getElementById('applyFilter');
    const resetFilter = document.getElementById('resetFilter');
    const filterStatus = document.getElementById('filterStatus');
    const filterAmount = document.getElementById('filterAmount');
    const filterEmployee = document.getElementById('filterEmployee');
    const requestCards = document.querySelectorAll('.advance-request-card');
    
    if (applyFilter && resetFilter) {
        applyFilter.addEventListener('click', function() {
            const statusValue = filterStatus.value;
            const amountValue = filterAmount.value;
            const employeeValue = filterEmployee ? filterEmployee.value : '';
            
            requestCards.forEach(card => {
                let showCard = true;
                
                if (statusValue && card.dataset.status !== statusValue) {
                    showCard = false;
                }
                
                if (amountValue) {
                    const amount = parseFloat(card.dataset.amount);
                    if (amountValue === '0-1000' && (amount > 1000 || amount <= 0)) {
                        showCard = false;
                    } else if (amountValue === '1000-3000' && (amount <= 1000 || amount > 3000)) {
                        showCard = false;
                    } else if (amountValue === '3000+' && amount <= 3000) {
                        showCard = false;
                    }
                }
                
                if (employeeValue && card.dataset.employee !== employeeValue) {
                    showCard = false;
                }
                
                card.style.display = showCard ? '' : 'none';
            });
            
            filterPanel.style.display = 'none';
        });
        
        resetFilter.addEventListener('click', function() {
            filterStatus.value = '';
            filterAmount.value = '';
            if (filterEmployee) filterEmployee.value = '';
            
            requestCards.forEach(card => {
                card.style.display = '';
            });
        });
    }
}

/**
 * Calculate advance statistics
 */
function calculateAdvanceStatistics() {
    const totalAdvanceElement = document.getElementById('total-advance');
    const approvedAdvanceElement = document.getElementById('approved-advance');
    const pendingAdvanceElement = document.getElementById('pending-advance');
    
    if (totalAdvanceElement && approvedAdvanceElement && pendingAdvanceElement) {
        const requestCards = document.querySelectorAll('.advance-request-card');
        let totalAmount = 0;
        let approvedAmount = 0;
        let pendingAmount = 0;
        
        requestCards.forEach(card => {
            const amount = parseFloat(card.dataset.amount);
            const status = card.dataset.status;
            
            totalAmount += amount;
            
            if (status === 'approved') {
                approvedAmount += amount;
            } else if (status === 'pending') {
                pendingAmount += amount;
            }
        });
        
        // Format amounts as currency
        const formatter = new Intl.NumberFormat('tr-TR', {
            style: 'currency',
            currency: 'TRY'
        });
        
        totalAdvanceElement.textContent = formatter.format(totalAmount);
        approvedAdvanceElement.textContent = formatter.format(approvedAmount);
        pendingAdvanceElement.textContent = formatter.format(pendingAmount);
    }
}