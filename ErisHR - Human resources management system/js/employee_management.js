/**
 * HRMS - İnsan Kaynakları Yönetim Sistemi
 * Employee Management JavaScript File
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize employee management functionality
    initEmployeeModals();
});

/**
 * Initialize employee management modals
 */
function initEmployeeModals() {
    // View Employee Modal
    document.querySelectorAll('[data-bs-target="#viewEmployeeModal"]').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const department = this.getAttribute('data-department');
            const position = this.getAttribute('data-position');
            const email = this.getAttribute('data-email');
            const phone = this.getAttribute('data-phone');
            const hireDate = this.getAttribute('data-hire-date');
            const annualLeave = this.getAttribute('data-annual-leave');
            const remainingLeave = this.getAttribute('data-remaining-leave');
            const status = this.getAttribute('data-status');
            
            document.getElementById('view-id').textContent = id;
            document.getElementById('view-name').textContent = name;
            document.getElementById('view-department').textContent = department;
            document.getElementById('view-position').textContent = position;
            document.getElementById('view-email').textContent = email;
            document.getElementById('view-phone').textContent = phone;
            document.getElementById('view-hire-date').textContent = hireDate;
            document.getElementById('view-annual-leave').textContent = annualLeave;
            document.getElementById('view-remaining-leave').textContent = remainingLeave;
            document.getElementById('view-status').textContent = status === 'active' ? 'Aktif' : 'Pasif';
        });
    });
    
    // Edit Employee Modal
    document.querySelectorAll('[data-bs-target="#editEmployeeModal"]').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const department = this.getAttribute('data-department');
            const position = this.getAttribute('data-position');
            const email = this.getAttribute('data-email');
            const phone = this.getAttribute('data-phone');
            const hireDate = this.getAttribute('data-hire-date');
            const managerId = this.getAttribute('data-manager-id');
            const annualLeave = this.getAttribute('data-annual-leave');
            const remainingLeave = this.getAttribute('data-remaining-leave');
            const status = this.getAttribute('data-status');
            
            document.getElementById('edit-employee-id').value = id;
            document.getElementById('edit-name').value = name;
            document.getElementById('edit-department').value = department;
            document.getElementById('edit-position').value = position;
            document.getElementById('edit-email').value = email;
            document.getElementById('edit-phone').value = phone;
            document.getElementById('edit-hire-date').value = hireDate;
            document.getElementById('edit-manager-id').value = managerId || '';
            document.getElementById('edit-annual-leave').value = annualLeave;
            document.getElementById('edit-remaining-leave').value = remainingLeave;
            document.getElementById('edit-status').value = status;
        });
    });
    
    // Delete Employee Modal
    document.querySelectorAll('[data-bs-target="#deleteEmployeeModal"]').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            
            document.getElementById('delete-employee-id').value = id;
            document.getElementById('delete-employee-name').textContent = name;
        });
    });
}