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

// Fetch the last added user from the database, including the password
$query = "SELECT first_name, last_name, gender, email, password FROM users ORDER BY id DESC LIMIT 1";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    $user_info = $result->fetch_assoc();
    echo json_encode([
        'success' => true,
        'first_name' => $user_info['first_name'],
        'last_name' => $user_info['last_name'],
        'gender' => $user_info['gender'],
        'email' => $user_info['email'],
        'password' => $user_info['password'] // Include the password in the response
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'No users found in the database.']);
}

$conn->close();
?>
