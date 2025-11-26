<?php
// Példa hívás:
// http://localhost/sensor/insert_data.php?api_key=YOUR_API_KEY&temperature=25.5&pressure=101325

// --- Adatbázis kapcsolat beállításai ---
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "sensor_data";

// --- Segédfüggvény: API kulcs beolvasása (X-API-Key header vagy GET paraméter) ---
function get_api_key(): ?string {
    // Headerből
    if (function_exists('getallheaders')) {
        $headers = getallheaders();
        if (isset($headers['X-API-Key']) && trim($headers['X-API-Key']) !== '') {
            return trim($headers['X-API-Key']);
        }
        // Egyes szerverek kisbetűsítik:
        if (isset($headers['x-api-key']) && trim($headers['x-api-key']) !== '') {
            return trim($headers['x-api-key']);
        }
    }
    // GET paraméterből
    if (isset($_GET['api_key']) && trim($_GET['api_key']) !== '') {
        return trim($_GET['api_key']);
    }
    return null;
}

// --- Kapcsolódás az adatbázishoz ---
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    die("Kapcsolódási hiba: " . $conn->connect_error);
}

// --- API kulcs ellenőrzése (ELSŐ LÉPÉS) ---
$apiKey = get_api_key();
if ($apiKey === null) {
    http_response_code(401);
    $conn->close();
    die("Hiányzó API kulcs! Add meg az X-API-Key fejléccel vagy az api_key paraméterrel.");
}

$check = $conn->prepare("SELECT id FROM api_keys WHERE api_key = ? AND is_active = 1 LIMIT 1");
$check->bind_param("s", $apiKey);
$check->execute();
$check->store_result();

if ($check->num_rows === 0) {
    http_response_code(401);
    $check->close();
    $conn->close();
    die("Érvénytelen vagy inaktív API kulcs!");
}
// opcionális: utolsó használat frissítése
$update = $conn->prepare("UPDATE api_keys SET last_used_at = NOW() WHERE api_key = ?");
$update->bind_param("s", $apiKey);
$update->execute();
$update->close();
$check->close();

// --- Paraméterek ellenőrzése és mentése ---
if (isset($_GET['temperature']) && isset($_GET['pressure'])) {
    $temperature = filter_var($_GET['temperature'], FILTER_VALIDATE_FLOAT);
    $pressure    = filter_var($_GET['pressure'], FILTER_VALIDATE_FLOAT);

    if ($temperature === false || $pressure === false) {
        http_response_code(400);
        echo "Érvénytelen paraméterek!";
    } else {
        $stmt = $conn->prepare("INSERT INTO measurements (temperature, pressure, created_at) VALUES (?, ?, NOW())");
        if (!$stmt) {
            http_response_code(500);
            echo "Előkészítési hiba: " . $conn->error;
        } else {
            $stmt->bind_param("dd", $temperature, $pressure);
            if ($stmt->execute()) {
                echo "Adatok sikeresen rögzítve.";
            } else {
                http_response_code(500);
                echo "Hiba történt: " . $stmt->error;
            }
            $stmt->close();
        }
    }
} else {
    http_response_code(400);
    echo "Hiányzó paraméterek! (temperature, pressure)";
}

$conn->close();
?>
