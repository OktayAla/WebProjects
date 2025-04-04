/**
 * HRMS - İnsan Kaynakları Yönetim Sistemi
 * Department Management JavaScript File
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize department management functionality
    initDepartmentModals();
});

/**
 * Initialize department management modals
 */
function initDepartmentModals() {
    // View Department Modal
    document.querySelectorAll('[data-bs-target="#viewDepartmentModal"]').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const manager = this.getAttribute('data-manager');
            const employeeCount = this.getAttribute('data-employee-count');
            
            document.getElementById('view-id').textContent = id;
            document.getElementById('view-name').textContent = name;
            document.getElementById('view-manager').textContent = manager;
            document.getElementById('view-employee-count').textContent = employeeCount;
        });
    });
    
    // Edit Department Modal
    document.querySelectorAll('[data-bs-target="#editDepartmentModal"]').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const managerId = this.getAttribute('data-manager-id');
            
            document.getElementById('edit-department-id').value = id;
            document.getElementById('edit-name').value = name;
            document.getElementById('edit-manager-id').value = managerId || '';
        });
    });
    
    // Delete Department Modal
    document.querySelectorAll('[data-bs-target="#deleteDepartmentModal"]').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            
            document.getElementById('delete-department-id').value = id;
            document.getElementById('delete-department-name').textContent = name;
        });
    });
}