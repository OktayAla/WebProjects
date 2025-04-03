// Dashboard sayfası için özel JS kodları
document.addEventListener('DOMContentLoaded', function() {
    // Grafikleri çiz
    if(document.getElementById('employeeChart')) {
        drawEmployeeChart();
    }
    if(document.getElementById('leaveChart')) {
        drawLeaveChart();
    }

    // Dashboard verilerini güncelle
    function updateDashboardData() {
        sendAjaxRequest('/api/dashboard/stats', 'GET', null, function(data) {
            document.getElementById('totalEmployees').textContent = data.totalEmployees;
            document.getElementById('pendingLeaves').textContent = data.pendingLeaves;
            document.getElementById('activeTasks').textContent = data.activeTasks;
        });
    }

    // Grafik çizme fonksiyonları
    function drawEmployeeChart() {
        const ctx = document.getElementById('employeeChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs', 'Haziran'],
                datasets: [{
                    label: 'Çalışan Sayısı',
                    data: [12, 19, 3, 5, 2, 3],
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Her 5 dakikada bir verileri güncelle
    setInterval(updateDashboardData, 300000);
});
