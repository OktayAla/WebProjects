from flask import Flask, render_template, request
import requests
from dotenv import load_dotenv
import os

load_dotenv()  # .env dosyasından API key yükle

app = Flask(__name__)
API_KEY = os.getenv("OPENWEATHER_API_KEY")

# Yeni: Türk şehirlerinin listesi
TURKISH_CITIES = ["Istanbul", "Ankara", "Izmir", "Bursa", "Adana", "Antalya", "Konya", "Gaziantep", "Kayseri", "Mersin"]

@app.route("/", methods=["GET", "POST"])
def home():
    weather_data = None
    forecast_data = None
    if request.method == "POST":
        city = request.form.get("city")
        weather_data = get_weather(city)
        forecast_data = get_forecast(city)
    # Tüm şehirler için hava durumu verileri
    cities_weather = get_cities_weather(TURKISH_CITIES)
    return render_template("index.html", weather=weather_data, forecast=forecast_data, cities_weather=cities_weather)
    
def get_weather(city):
    base_url = "http://api.openweathermap.org/data/2.5/weather"
    params = {
        "q": city,
        "appid": API_KEY,
        "units": "metric",  # Celsius için
        "lang": "tr"
    }
    response = requests.get(base_url, params=params)
    return response.json() if response.status_code == 200 else None

def get_forecast(city):
    base_url = "http://api.openweathermap.org/data/2.5/forecast"
    params = {
        "q": city,
        "appid": API_KEY,
        "units": "metric",
        "lang": "tr"
    }
    response = requests.get(base_url, params=params)
    if response.status_code == 200:
        data = response.json()
        return data.get("list", [])[:5]
    return None

def get_cities_weather(cities):
    results = []
    for city in cities:
        data = get_weather(city)
        if data:
            results.append(data)
    return results

if __name__ == "__main__":
    app.run(debug=True)