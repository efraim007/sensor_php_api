import requests
import time
import random
from time import sleep

apikey="123456"
# Adatok küldése a saját php api-ra
def send_data_to_api(temp, press):
    #url = f'https://api.thingspeak.com/update?api_key={API_KEY}&field1={temp}&field2={pressure}'
    url = f'http://localhost/sensor/insert_data_apikey.php?api_key={apikey}&temperature={temperature}&pressure={pressure}'
    #http://localhost/sensor/insert_data_apikey.php?api_key=123456&temperature=25.5&pressure=1010
    response = requests.get(url)
    
    if response.status_code == 200:
        print(f"Sikeres adatküldés: Hőmérséklet={temperature}, Légnyomás={pressure}")
    else:
        print(f"Sikertelen adatküldés: {response.status_code}")



# Végtelen ciklus 10 másodperces időközönként
while True:
    
    # Véletlenszerű hőmérséklet és páratartalom generálása
    temperature = round(random.uniform(15.0, 25.0), 2)  # Példa: 20-30°C között
    pressure = round(random.uniform(1015.0, 1025.0), 2)     # Példa: 30-70% között


    print(f"{temperature:05.2f}*C {pressure:05.2f}hPa")

    
    # Adatok küldése
    send_data_to_api(temperature, pressure)
    
    # 10 másodperces várakozás
    time.sleep(10)
