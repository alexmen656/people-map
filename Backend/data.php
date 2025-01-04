<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$config = include('config.php');
$host = $config['host'];
$dbname = $config['dbname'];
$username = $config['username'];
$password = $config['password'];

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->query("SELECT country, COUNT(*) as user_count FROM users GROUP BY country");
        $userCounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($userCounts);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $country = $_POST['country'];
    $ip_address = $_SERVER['REMOTE_ADDR'];

    if ($country) {
        try {
            // Disabled because two users could have the same public IP
            /*   $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE ip_address = :ip_address");
               $stmt->bindParam(':ip_address', $ip_address);
               $stmt->execute();
               $count = $stmt->fetchColumn();

               if ($count == 0) {*/
            $stmt = $pdo->prepare("INSERT INTO users (country) VALUES (:country)");//, ip_address | , :ip_address
            $stmt->bindParam(':country', $country);
            //$stmt->bindParam(':ip_address', $ip_address);
            $stmt->execute();
            echo json_encode(['status' => 'success', 'message' => 'User added successfully']);
            /*  } else {
                  echo json_encode(['status' => 'error', 'message' => 'IP address has already submitted data']);
              }*/
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    }
}
?>