<?php
// Allow CORS
header("Access-Control-Allow-Origin: *");
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
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// Handle POST method (saving account information)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get data from POST request (supports both form data and JSON body)
    $data = json_decode(file_get_contents("php://input"), true);

    $first_name = $data['first_name'] ?? null;
    $last_name = $data['last_name'] ?? null;
    $gender = $data['gender'] ?? null;
    $email = $data['email'] ?? null;
    $password = $data['password'] ?? null;

    // Validate data
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
        http_response_code(400); // Bad Request
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400); // Bad Request
        echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
        exit;
    }

    // Validate password strength
    if (strlen($password) < 8) {
        http_response_code(400); // Bad Request
        echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters long.']);
        exit;
    }

    // Check if the email already exists
    $stmt_check = $conn->prepare("SELECT email FROM users WHERE email = ?");
    if (!$stmt_check) {
        echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
        exit;
    }

    $stmt_check->bind_param('s', $email);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        http_response_code(400); // Bad Request
        echo json_encode(['success' => false, 'message' => 'Email already exists.']);
        exit;
    }
    $stmt_check->close();



    // Insert data into the database
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, gender, email, password) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
        exit;
    }

    $stmt->bind_param('sssss', $first_name, $last_name, $gender, $email, $password);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Account information saved successfully.']);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(['success' => false, 'message' => 'Execution failed: ' . $stmt->error]);
    }

    $stmt->close();
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Only POST method is allowed.']);
}

$conn->close();
?>
