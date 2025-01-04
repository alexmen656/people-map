<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Methods: *');

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
    die("Could not connect to the database: " . $e->getMessage());
}

// Handle GET request to fetch user count by country
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->query("SELECT country, COUNT(*) as user_count FROM users GROUP BY country");
        $userCounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($userCounts);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Handle POST request to insert new user data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $country = $_POST['country'];
    $ip_address = $_SERVER['REMOTE_ADDR'];

    if ($country) {
        try {
            // Check if the IP address has already submitted data
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE ip_address = :ip_address");
            $stmt->bindParam(':ip_address', $ip_address);
            $stmt->execute();
            $count = $stmt->fetchColumn();

            if ($count == 0) {
                $stmt = $pdo->prepare("INSERT INTO users (country, ip_address) VALUES (:country, :ip_address)");
                $stmt->bindParam(':country', $country);
                $stmt->bindParam(':ip_address', $ip_address);
                $stmt->execute();
                echo json_encode(['status' => 'success', 'message' => 'User added successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'IP address has already submitted data']);
            }
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    }
}
?>