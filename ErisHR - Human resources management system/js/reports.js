/**
 * HRMS - İnsan Kaynakları Yönetim Sistemi
 * Reports JavaScript File
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize charts if we're on the reports page
    if (document.getElementById('departmentChart')) {
        initDepartmentChart();
        initLeaveChart();
        initAttendanceChart();
        initLateEntryChart();
    }
});

/**
 * Initialize Department Distribution Chart
 */
function initDepartmentChart() {
    const departmentCtx = document.getElementById('departmentChart').getContext('2d');
    const departmentLabels = JSON.parse(document.getElementById('department-data-labels').textContent);
    const departmentValues = JSON.parse(document.getElementById('department-data-values').textContent);
    
    const departmentChart = new Chart(departmentCtx, {
        type: 'pie',
        data: {
            labels: departmentLabels,
            datasets: [{
                data: departmentValues,
                backgroundColor: [
                    '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#5a5c69', '#858796',
                    '#6610f2', '#6f42c1', '#fd7e14', '#20c9a6', '#27a844'
                ],
                hoverBackgroundColor: [
                    '#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#be2617', '#3a3b45', '#60616f',
                    '#5d0cdb', '#5d37a8', '#dc6a03', '#169b80', '#1e8e39'
                ],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }]
        },
        options: {
            maintainAspectRatio: false,
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                caretPadding: 10,
            },
            legend: {
                display: true,
                position: 'bottom'
            },
            cutoutPercentage: 0,
        },
    });
}

/**
 * Initialize Monthly Leave Usage Chart
 */
function initLeaveChart() {
    const leaveCtx = document.getElementById('leaveChart').getContext('2d');
    const leaveLabels = JSON.parse(document.getElementById('leave-data-labels').textContent);
    const leaveValues = JSON.parse(document.getElementById('leave-data-values').textContent);
    
    const leaveChart = new Chart(leaveCtx, {
        type: 'bar',
        data: {
            labels: leaveLabels,
            datasets: [{
                label: 'Kullanılan İzin Günleri',
                data: leaveValues,
                backgroundColor: '#4e73df',
                borderColor: '#4e73df',
                borderWidth: 1
            }]
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });
}

/**
 * Initialize Monthly Attendance Chart
 */
function initAttendanceChart() {
    const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
    const attendanceLabels = JSON.parse(document.getElementById('attendance-data-labels').textContent);
    const attendanceValues = JSON.parse(document.getElementById('attendance-data-values').textContent);
    
    const attendanceChart = new Chart(attendanceCtx, {
        type: 'line',
        data: {
            labels: attendanceLabels,
            datasets: [{
                label: 'Toplam Giriş Sayısı',
                data: attendanceValues,
                backgroundColor: 'rgba(28, 200, 138, 0.2)',
                borderColor: '#1cc88a',
                borderWidth: 2,
                pointBackgroundColor: '#1cc88a',
                pointBorderColor: '#1cc88a',
                pointHoverBackgroundColor: '#1cc88a',
                pointHoverBorderColor: '#1cc88a',
                pointRadius: 3,
                pointHoverRadius: 5,
                fill: true
            }]
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });
}

/**
 * Initialize Late Entry Chart
 */
function initLateEntryChart() {
    const lateEntryCtx = document.getElementById('lateEntryChart').getContext('2d');
    const lateEntryLabels = JSON.parse(document.getElementById('late-entry-data-labels').textContent);
    const lateEntryValues = JSON.parse(document.getElementById('late-entry-data-values').textContent);
    
    const lateEntryChart = new Chart(lateEntryCtx, {
        type: 'bar',
        data: {
            labels: lateEntryLabels,
            datasets: [{
                label: 'Geç Giriş Sayısı',
                data: lateEntryValues,
                backgroundColor: '#e74a3b',
                borderColor: '#e74a3b',
                borderWidth: 1
            }]
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        }
    });
}