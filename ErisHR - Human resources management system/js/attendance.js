/**
 * HRMS - İnsan Kaynakları Yönetim Sistemi
 * Attendance JavaScript File
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize attendance date filters
    initAttendanceFilters();
});

/**
 * Initialize attendance date filters
 */
function initAttendanceFilters() {
    // Date filtering for personal attendance
    const filterToday = document.getElementById('filterToday');
    const filterWeek = document.getElementById('filterWeek');
    const filterMonth = document.getElementById('filterMonth');
    
    if (filterToday) {
        filterToday.addEventListener('click', function() {
            filterAttendanceByDate('today', 'attendanceTable');
        });
    }
    
    if (filterWeek) {
        filterWeek.addEventListener('click', function() {
            filterAttendanceByDate('week', 'attendanceTable');
        });
    }
    
    if (filterMonth) {
        filterMonth.addEventListener('click', function() {
            filterAttendanceByDate('month', 'attendanceTable');
        });
    }
    
    // Date filtering for team attendance
    const teamFilterToday = document.getElementById('teamFilterToday');
    const teamFilterWeek = document.getElementById('teamFilterWeek');
    const teamFilterMonth = document.getElementById('teamFilterMonth');
    
    if (teamFilterToday) {
        teamFilterToday.addEventListener('click', function() {
            filterAttendanceByDate('today', 'teamAttendanceTable');
        });
    }
    
    if (teamFilterWeek) {
        teamFilterWeek.addEventListener('click', function() {
            filterAttendanceByDate('week', 'teamAttendanceTable');
        });
    }
    
    if (teamFilterMonth) {
        teamFilterMonth.addEventListener('click', function() {
            filterAttendanceByDate('month', 'teamAttendanceTable');
        });
    }
}

/**
 * Filter attendance records by date period
 * @param {string} period - The period to filter by (today, week, month)
 * @param {string} tableId - The ID of the table to filter
 */
function filterAttendanceByDate(period, tableId) {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    const rows = table.querySelectorAll('tbody tr');
    
    const today = new Date();
    const oneWeekAgo = new Date();
    oneWeekAgo.setDate(today.getDate() - 7);
    const oneMonthAgo = new Date();
    oneMonthAgo.setMonth(today.getMonth() - 1);
    
    rows.forEach(row => {
        const dateStr = row.getAttribute('data-date');
        if (!dateStr) return;
        
        const rowDate = new Date(dateStr);
        
        if (period === 'today') {
            if (rowDate.toDateString() === today.toDateString()) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        } else if (period === 'week') {
            if (rowDate >= oneWeekAgo) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        } else if (period === 'month') {
            if (rowDate >= oneMonthAgo) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    });
}