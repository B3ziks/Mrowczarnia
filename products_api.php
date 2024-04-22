<?php
header('Content-Type: application/json; charset=utf-8');

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'Formutor');
define('DB_PASSWORD', 'Cr7OHKpuVpkzWaR');
define('DB_NAME', 'formutor');

// Create connection
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($link === false) {
    echo json_encode(['error' => "ERROR: Could not connect. " . mysqli_connect_error()]);
    exit;
}

// Check if an ID was provided for a single product detail
$product_id = isset($_GET['id']) ? $_GET['id'] : null;

if ($product_id) {
    // SQL to get details of a single product
    $stmt = mysqli_prepare($link, "SELECT id, name, price, description, sale_price, quantity FROM produkty WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($product = mysqli_fetch_assoc($result)) {
        echo json_encode($product);
    } else {
        echo json_encode(['message' => 'No product found']);
    }
    mysqli_free_result($result);
} else {
    // SQL to get all products
    $sql = "SELECT id, name, price, description, sale_price, quantity FROM produkty";
    $result = mysqli_query($link, $sql);

    if ($result) {
        $products = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }
        mysqli_free_result($result);
        echo json_encode($products);
    } else {
        echo json_encode(['error' => "SQL Error: " . mysqli_error($link)]);
    }
}

mysqli_close($link);
?>
