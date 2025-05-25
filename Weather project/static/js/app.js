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

document.addEventListener('DOMContentLoaded', async function() {
    // SVG haritayı yükle
    const response = await fetch('/static/js/turkey-map.svg');
    const svgText = await response.text();
    document.getElementById('turkey-map-container').innerHTML = svgText;

    const popup = document.getElementById('weather-popup');
    const provinces = document.querySelectorAll('.province');

    provinces.forEach(province => {
        province.addEventListener('mouseenter', (e) => {
            const cityName = province.getAttribute('data-name');
            const cityWeather = citiesWeather.find(w => w.name.toLowerCase() === cityName.toLowerCase());
            
            if (cityWeather) {
                showWeatherPopup(cityWeather, e);
            }
        });

        province.addEventListener('mouseleave', () => {
            hideWeatherPopup();
        });
    });
});

function showWeatherPopup(weatherData, event) {
    const popup = document.getElementById('weather-popup');
    popup.querySelector('.city-name').textContent = weatherData.name;
    popup.querySelector('.temperature').textContent = `${Math.round(weatherData.main.temp)}°C`;
    popup.querySelector('.description').textContent = weatherData.weather[0].description;
    popup.querySelector('.weather-icon').innerHTML = 
        `<img src="http://openweathermap.org/img/wn/${weatherData.weather[0].icon}.png" alt="hava durumu">`;
    popup.querySelector('.details').innerHTML = 
        `Nem: ${weatherData.main.humidity}% | Rüzgar: ${weatherData.wind.speed} km/s`;

    // Popup pozisyonunu ayarla
    const rect = event.target.getBoundingClientRect();
    popup.style.left = `${rect.left + rect.width/2}px`;
    popup.style.top = `${rect.top - popup.offsetHeight - 10}px`;
    popup.classList.add('active');
}

function hideWeatherPopup() {
    document.getElementById('weather-popup').classList.remove('active');
}