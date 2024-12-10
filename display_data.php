<?php
//http://localhost/sensor/display_data.php
// Adatbázis kapcsolat beállításai
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sensor_data";

// Kapcsolódás az adatbázishoz
$conn = new mysqli($servername, $username, $password, $dbname);

// Kapcsolódási hiba ellenőrzése
if ($conn->connect_error) {
    die("Kapcsolódási hiba: " . $conn->connect_error);
}

// Adatok lekérése
$sql = "SELECT temperature, pressure, created_at FROM measurements ORDER BY created_at ASC";
$result = $conn->query($sql);

$temperatures = [];
$pressures = [];
$timestamps = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $temperatures[] = $row['temperature'];
        $pressures[] = $row['pressure'];
        $timestamps[] = $row['created_at'];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adatok megjelenítése</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <h1>Adatok grafikonon</h1>
    <canvas id="temperatureChart" width="800" height="400"></canvas>
    <canvas id="pressureChart" width="800" height="400"></canvas>

    <script>
        const timestamps = <?php echo json_encode($timestamps); ?>;
        const temperatures = <?php echo json_encode($temperatures); ?>;
        const pressures = <?php echo json_encode($pressures); ?>;

        // Hőmérséklet grafikon
        new Chart(document.getElementById('temperatureChart'), {
            type: 'line',
            data: {
                labels: timestamps,
                datasets: [{
                    label: 'Hőmérséklet (°C)',
                    data: temperatures,
                    borderColor: 'red',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Időpont'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Hőmérséklet (°C)'
                        }
                    }
                }
            }
        });

        // Nyomás grafikon
        new Chart(document.getElementById('pressureChart'), {
            type: 'line',
            data: {
                labels: timestamps,
                datasets: [{
                    label: 'Nyomás (Pa)',
                    data: pressures,
                    borderColor: 'blue',
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Időpont'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Nyomás (Pa)'
                        },
                        min: 1000, // Alsó határ
                        max: 1200  // Felső határ
                    }
                }
            }
        });
    </script>
</body>
</html>
