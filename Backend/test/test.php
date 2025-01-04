<?php
// Include database configuration
$config = include('../config.php');
$host = $config['host'];
$dbname = $config['dbname'];
$username = $config['username'];
$password = $config['password'];

// Create a new PDO instance
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

// Function to insert users
function insertUsers($pdo, $country, $count) {
    $stmt = $pdo->prepare("INSERT INTO users (country, ip_address) VALUES (:country, :ip_address)");
    for ($i = 0; $i < $count; $i++) {
        $stmt->execute([
            ':country' => $country,
            ':ip_address' => 'testing'
        ]);
    }
}

// Function to delete test users
function deleteTestUsers($pdo) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE ip_address = 'testing'");
    $stmt->execute();
}

// Check if delete parameter is set
if (isset($_GET['delete'])) {
    deleteTestUsers($pdo);
    echo "Test users deleted successfully.";
} else {
    // Delete existing test users before inserting new ones
    deleteTestUsers($pdo);

    // Insert users for each country
    insertUsers($pdo, 'Germany', 12);
    insertUsers($pdo, 'Slovakia', 3);
    insertUsers($pdo, 'Angola', 34);
    insertUsers($pdo, 'Australia', 82);
    insertUsers($pdo, 'India', 112);

    echo "Users inserted successfully.";
}
?>