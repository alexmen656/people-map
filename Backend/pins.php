<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST, GET');
header('Access-Control-Allow-Headers: Content-Type');

$config = include('config.php');
$host = $config['host'];
$dbname = $config['dbname'];
$username = $config['username'];
$password = $config['password'];
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Fetch pins data
    $sql = "SELECT * FROM pins";
    $result = $conn->query($sql);

    $pins = array();
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $pins[] = $row;
        }
    } else {
        echo json_encode(array("message" => "No pins found"));
        exit();
    }

    echo json_encode($pins);
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add a new pin
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['latitude']) && isset($data['longitude'])) {// && isset($data['description'])
        $title = $data['title'];
        $latitude = $data['latitude'];
        $longitude = $data['longitude'];
        //$description = $data['description'];

        $stmt = $conn->prepare("INSERT INTO pins (title, latitude, longitude) VALUES (?, ?, ?)");//, description, ?
        $stmt->bind_param("sdd", $title, $latitude, $longitude);//, $description

        if ($stmt->execute()) {
            echo json_encode(array("message" => "Pin added successfully"));
        } else {
            echo json_encode(array("message" => "Failed to add pin"));
        }

        $stmt->close();
    } else {
        echo json_encode(array("message" => "Invalid input"));
    }
}

$conn->close();
?>