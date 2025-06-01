<?php
// Allow CORS
header("Access-Control-Allow-Origin: *"); // Allow all origins (Change to specific domains for security)
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Handle preflight request (OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200); // Preflight response
    exit;
}

// Database connection details
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'webbuy_db';

// Create a MySQL connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

// Handle POST method only
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle POST request (add product to database)
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['title']) || !isset($data['price'])) {
        http_response_code(400); // Bad Request
        echo json_encode(['success' => false, 'message' => 'Product title and price are required.']);
        exit;
    }

    $title = $data['title'];
    $price = $data['price'];

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO products (title, price) VALUES (?, ?)");
    $stmt->bind_param('sd', $title, $price); // 's' for string, 'd' for double

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Product added successfully']);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(['success' => false, 'message' => 'Failed to add product']);
    }
    $stmt->close();
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Only POST method is allowed.']);
}

$conn->close();
?>
