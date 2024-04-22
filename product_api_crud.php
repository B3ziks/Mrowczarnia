<?php
header('Content-Type: application/json');

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'Formutor');
define('DB_PASSWORD', 'Cr7OHKpuVpkzWaR');
define('DB_NAME', 'formutor');

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    die(json_encode(['error' => "Connection failed: " . $conn->connect_error]));
}

if (!isAdmin($conn)) {
    http_response_code(403);
    echo json_encode(['error' => 'Nieautoryzowany — odmowa dostępu']);
    exit;
}

switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        handlePostRequest($conn);
        break;
    case 'DELETE':
        deleteProduct($conn);
        break;
    default:
        echo json_encode(['message' => 'Request method not supported']);
        break;
}

$conn->close();

function isAdmin($conn) {
    $apiKey = $_SERVER['HTTP_X_API_KEY'] ?? '';
    if (!$apiKey) {
        return false;
    }
    $stmt = $conn->prepare("SELECT role FROM api_keys WHERE api_key = ?");
    $stmt->bind_param("s", $apiKey);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    return $result->fetch_assoc()['role'] === 'admin';
}

function handlePostRequest($conn) {
    if (isset($_GET['action']) && $_GET['action'] === 'update') {
        updateProduct($conn);
    } else {
        addProduct($conn);
    }
}

function addProduct($conn) {
    $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

    if ($contentType === "application/json") {
        // Handle JSON content type
        $data = json_decode(file_get_contents("php://input"), true);
    } else {
        // Handle form data content type
        $data = $_POST;
    }

    // Check for required parameters
    if (empty($data['name']) || empty($data['description']) || empty($data['price']) || empty($data['quantity'])) {
        echo json_encode(['error' => 'Missing required parameters']);
        return;
    }

    // Optional photo handling
    $photo_data = null;
    if (isset($data['photo'])) {
        $photo_data = $data['photo'];
    } elseif (!empty($_FILES['photo']['tmp_name'])) {
        $photo_data = file_get_contents($_FILES['photo']['tmp_name']);
    }

    // Prepare and bind parameters accordingly
    $sql = "INSERT INTO produkty (name, description, price, sale_price, quantity" . ($photo_data ? ", photo" : "") . ") VALUES (?, ?, ?, ?, ?" . ($photo_data ? ", ?" : "") . ")";
    $stmt = $conn->prepare($sql);
    
    if ($photo_data) {
        $stmt->bind_param("sssdss", $data['name'], $data['description'], $data['price'], $data['sale_price'], $data['quantity'], $photo_data);
    } else {
        $stmt->bind_param("ssdds", $data['name'], $data['description'], $data['price'], $data['sale_price'], $data['quantity']);
    }

    if ($stmt->execute()) {
        echo json_encode(['message' => 'Product added successfully']);
    } else {
        echo json_encode(['error' => "Error adding product: " . $stmt->error]);
    }
    $stmt->close();
}



function updateProduct($conn) {
    $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';

    if ($contentType === "application/json") {
        // Handle JSON content type
        $data = json_decode(file_get_contents("php://input"), true);
    } else {
        // Handle form data content type
        $data = $_POST;
    }

    // Check for required parameters
    if (empty($data['name']) || empty($data['description']) || empty($data['price']) || empty($data['quantity']) || !isset($_GET['id'])) {
        echo json_encode(['error' => 'Missing required parameters']);
        return;
    }

    $product_id = $_GET['id'];

    // Check if a new photo has been uploaded
    $photo_data = null;
    if (isset($data['photo'])) {
        $photo_data = $data['photo'];
    } elseif (!empty($_FILES['photo']['tmp_name'])) {
        $photo_data = file_get_contents($_FILES['photo']['tmp_name']);
    }

    // Construct the SQL based on whether a new photo is provided
    if ($photo_data) {
        $sql = "UPDATE produkty SET name=?, description=?, price=?, sale_price=?, quantity=?, photo=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssddssi", $data['name'], $data['description'], $data['price'], $data['sale_price'], $data['quantity'], $photo_data, $product_id);
    } else {
        $sql = "UPDATE produkty SET name=?, description=?, price=?, sale_price=?, quantity=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssddsi", $data['name'], $data['description'], $data['price'], $data['sale_price'], $data['quantity'], $product_id);
    }

    if ($stmt->execute()) {
        echo json_encode(['message' => 'Product updated successfully']);
    } else {
        echo json_encode(['error' => "Error updating product: " . $stmt->error]);
    }
    $stmt->close();
}


function deleteProduct($conn) {
    if (!isset($_GET['id'])) {
        echo json_encode(['error' => 'Missing product ID for deletion']);
        return;
    }

    $stmt = $conn->prepare("DELETE FROM produkty WHERE id = ?");
    $stmt->bind_param("i", $_GET['id']);
    if ($stmt->execute()) {
        echo json_encode(['message' => 'Product deleted successfully']);
    } else {
        echo json_encode(['error' => "Error deleting product: " . $stmt->error]);
    }
    $stmt->close();
}
?>
