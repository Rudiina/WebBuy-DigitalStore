<?php
// Allow CORS
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Database connection details
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'webbuy_db';

// Create a MySQL connection
$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// Fetch all users from the database including passwords
$query = "SELECT first_name, last_name, gender, email, password FROM users";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row; // Include all columns in the response
    }
    echo json_encode(['success' => true, 'users' => $users]);
} else {
    echo json_encode(['success' => false, 'message' => 'No users found in the database.']);
}

$conn->close();
?>
