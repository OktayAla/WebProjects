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