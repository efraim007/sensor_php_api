<?php
//http://localhost/sensor/insert_data.php?temperature=25.5&pressure=101325
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

// Paraméterek ellenőrzése és mentése
if (isset($_GET['temperature']) && isset($_GET['pressure'])) {
    $temperature = filter_var($_GET['temperature'], FILTER_VALIDATE_FLOAT);
    $pressure = filter_var($_GET['pressure'], FILTER_VALIDATE_FLOAT);

    if ($temperature === false || $pressure === false) {
        echo "Érvénytelen paraméterek!";
    } else {
        $stmt = $conn->prepare("INSERT INTO measurements (temperature, pressure, created_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("dd", $temperature, $pressure);

        if ($stmt->execute()) {
            echo "Adatok sikeresen rögzítve.";
        } else {
            echo "Hiba történt: " . $stmt->error;
        }

        $stmt->close();
    }
} else {
    echo "Hiányzó paraméterek!";
}

$conn->close();
?>
