import requests
import time
import random

# ThingSpeak API kulcs
#API_KEY = 'HX1HIJ1Y9Q6CMGTV'

# Adatok küldése
def send_data_to_thingspeak(temp, press):
    #url = f'https://api.thingspeak.com/update?api_key={API_KEY}&field1={temp}&field2={humidity}'
    url = f'http://localhost/sensor/insert_data.php?temperature={temp}&pressure={press}'
    
    response = requests.get(url)
    
    if response.status_code == 200:
        print(f"Sikeres adatküldés: Hőmérséklet={temp}, Légnyomás={press}")
    else:
        print(f"Sikertelen adatküldés: {response.status_code}")

# Végtelen ciklus 10 másodperces időközönként
while True:
    # Véletlenszerű hőmérséklet és páratartalom generálása
    temperature = round(random.uniform(10.0, 50.0), 2)  # Példa: 10-50°C között
    pressure = round(random.uniform(1000.0, 1200.0), 2)     # Példa: 1000-2000 között
    
    # Adatok küldése
    send_data_to_thingspeak(temperature, pressure)
    
    # 10 másodperces várakozás
    time.sleep(10)
