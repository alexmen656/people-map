<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$json_file_path = 'data/countries.geojson';
$config = include('config.php');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(['error' => 'Could not connect to the database: ' . $e->getMessage()]));
}

$userCounts = [];
try {
    $stmt = $pdo->query("SELECT country, COUNT(*) as user_count FROM users GROUP BY country");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $userCounts[$row['country']] = $row['user_count'];
    }
} catch (PDOException $e) {
    die(json_encode(['error' => 'Error fetching user counts: ' . $e->getMessage()]));
}

if (file_exists($json_file_path)) {
    $json_data = file_get_contents($json_file_path);
    $geojson = json_decode($json_data, true);

    foreach ($geojson['features'] as &$feature) {
        $country = $feature['properties']['name'];
        $feature['properties']['count'] = isset($userCounts[$country]) ? $userCounts[$country] : 0;
    }

    echo json_encode($geojson);
} else {
    echo json_encode(['error' => 'Datei nicht gefunden']);
}
?>