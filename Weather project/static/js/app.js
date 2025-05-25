const ctx = document.getElementById('weatherChart').getContext('2d');
const chart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ['Bugün', 'Yarın', '3.Gün', '4.Gün', '5.Gün'],
        datasets: [{
            label: 'Sıcaklık (°C)',
            data: [22, 24, 19, 18, 21],
            borderColor: 'rgba(75, 192, 192, 1)',
            tension: 0.1
        }]
    }
});

if (typeof forecastData !== 'undefined' && forecastData) {
    const labels = forecastData.map(item => {
        // Saat olarak kesit (örn: 12:00)
        const d = new Date(item.dt * 1000);
        return d.getHours() + ":00";
    });
    const temps = forecastData.map(item => item.main.temp);
    const ctx = document.getElementById('forecastChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Sıcaklık (°C)',
                data: temps,
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true
        }
    });
}