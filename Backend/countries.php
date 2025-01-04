<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$json_file_path = 'countries.geojson';

// Database configuration
$host = '*****';
$dbname = '*****';
$username = '*****';
$password = '*****';

// Create a new PDO instance
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(['error' => 'Could not connect to the database: ' . $e->getMessage()]));
}

// Fetch user count by country
$userCounts = [];
try {
    $stmt = $pdo->query("SELECT country, COUNT(*) as user_count FROM users GROUP BY country");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $userCounts[$row['country']] = $row['user_count'];
    }
} catch (PDOException $e) {
    die(json_encode(['error' => 'Error fetching user counts: ' . $e->getMessage()]));
}

// Überprüfe, ob die Datei existiert
if (file_exists($json_file_path)) {
    // Lese die Datei und dekodiere sie
    $json_data = file_get_contents($json_file_path);
    $geojson = json_decode($json_data, true);

    // Füge die Benutzeranzahl zu den Eigenschaften hinzu
    foreach ($geojson['features'] as &$feature) {
        $country = $feature['properties']['name'];
        $feature['properties']['count'] = isset($userCounts[$country]) ? $userCounts[$country] : 0;
    }

    // Gebe die modifizierte GeoJSON-Datei aus
    echo json_encode($geojson);
} else {
    // Fehlerbehandlung, falls die Datei nicht existiert
    echo json_encode(['error' => 'Datei nicht gefunden']);
}
?>
