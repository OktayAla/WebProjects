from flask import Flask, render_template, request
import requests
from dotenv import load_dotenv
import os

load_dotenv()  # .env dosyasından API key yükle

app = Flask(__name__)
API_KEY = os.getenv("OPENWEATHER_API_KEY")

@app.route("/", methods=["GET", "POST"])
def home():
    if request.method == "POST":
        city = request.form.get("city")
        weather_data = get_weather(city)
        return render_template("index.html", weather=weather_data)
    return render_template("index.html")

def get_weather(city):
    base_url = "http://api.openweathermap.org/data/2.5/weather"
    params = {
        "q": city,
        "appid": API_KEY,
        "units": "metric",  # Celsius için
        "lang": "tr"       # Türkçe çıktı
    }
    response = requests.get(base_url, params=params)
    return response.json() if response.status_code == 200 else None

if __name__ == "__main__":
    app.run(debug=True)