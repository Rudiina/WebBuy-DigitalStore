<?php
// Allow CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
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

// Handle GET request to fetch cart items (Only title and price)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Query to fetch title and price only
    $sql = "SELECT title, price FROM products";  // or cart, depending on where you want to get the data
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $items = [];
        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
        echo json_encode(['success' => true, 'items' => $items]);
    } else {
        echo json_encode(['success' => true, 'items' => []]); // No items in the cart or products
    }
}

// Handle POST request to add a new item to the cart
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the data from the request body (in JSON format)
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['product_id']) || !isset($data['title']) || !isset($data['price'])) {
        http_response_code(400); // Bad Request
        echo json_encode(['success' => false, 'message' => 'Product ID, title, and price are required.']);
        exit;
    }

    $product_id = $data['product_id'];
    $title = $data['title'];
    $price = $data['price'];

    // Insert the new item into the cart table
    $stmt = $conn->prepare("INSERT INTO cart (product_id, title, price) VALUES (?, ?, ?)");
    $stmt->bind_param('isd', $product_id, $title, $price); // 'i' for integer, 's' for string, 'd' for double

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Product added to cart successfully.']);
    } else {
        http_response_code(500); // Internal Server Error
        echo json_encode(['success' => false, 'message' => 'Failed to add product to cart.']);
    }
    $stmt->close();
}

$conn->close();
?>
