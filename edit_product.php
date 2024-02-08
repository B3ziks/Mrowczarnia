<?php
session_start(); 

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== "admin") {
    header("location: login.php");
    exit;
}

    define('DB_SERVER', 'localhost');
    define('DB_USERNAME', 'Admin');
    define('DB_PASSWORD', 'Cr7OHKpuVpkzWaR');
    define('DB_NAME', 'mrowczarnia');


try {
    $conn = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $product_id = isset($_GET['id']) ? $_GET['id'] : 0;

    if ($_SERVER["REQUEST_METHOD"] == "GET" && $product_id) {
        $stmt = $conn->prepare("SELECT * FROM produkty WHERE id = :id");
        $stmt->execute(['id' => $product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
    }

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    // Assign the posted values to variables
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $sale_price = $_POST['sale_price'];
    $quantity = $_POST['quantity'];

    // Prepare the CALL statement to invoke the stored procedure
    $stmt = $conn->prepare("CALL update_product(?, ?, ?, ?, ?, ?)");
    
    // Bind the parameters and execute
    $stmt->bindParam(1, $product_id, PDO::PARAM_INT);
    $stmt->bindParam(2, $name, PDO::PARAM_STR);
    $stmt->bindParam(3, $description, PDO::PARAM_STR);
    $stmt->bindParam(4, $price);
    $stmt->bindParam(5, $sale_price);
    $stmt->bindParam(6, $quantity, PDO::PARAM_INT);
    
    if ($stmt->execute()) {
        header("Location: product_details.php?id=" . $product_id);
        exit();
    } else {
        echo "Error updating record: " . $stmt->errorInfo()[2];
    }
}

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$conn = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<title>Mrówczarnia - Edycja produktu</title>
  <link rel="icon" type="image/x-icon" href="favicon.png">
<link rel="stylesheet" href="styledetails.css">

</head>
<body>
<div class="header">
  <a href="home.html"><img src="mrowkaTitle.png" alt="Home"></a>
  <h1 class="shop-name">Mrówczarnia</h1>
  <div style="flex-grow: 1;"></div>
</div>

    <div class="navbar">
        <a href="index.php">Strona Główna<img src="anticon.png" alt="Home"></a>
        <a href="koszyk.php">Koszyk <img src="koszyk.png" alt="Koszyk"></a>
        <a href="profil.php">Konto <img src="anthead.png" alt="Konto"></a>
    </div>

<div id="productDetails">
    <!-- Product Edit Form -->
    <form method="post" action="edit_product.php?id=<?= htmlspecialchars($product_id) ?>">
        <div class="product-info">
            <h1>Edytuj Produkt</h1>
            <label for="name">Nazwa:</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($product['name']) ?>"><br><br>

            <label for="description">Opis:</label>
            <textarea id="description" name="description"><?= htmlspecialchars($product['description']) ?></textarea><br><br>

            <label for="price">Cena:</label>
            <input type="text" id="price" name="price" value="<?= htmlspecialchars($product['price']) ?>"><br><br>

            <label for="sale_price">Cena (promocja):</label>
            <input type="text" id="sale_price" name="sale_price" value="<?= htmlspecialchars($product['sale_price']) ?>"><br><br>

<label for="quantity">Ilość:</label>
<input type="number" id="quantity" name="quantity" value="<?= htmlspecialchars($product['quantity']) ?>"><br><br>

            <button type="submit" name="update" class="button">Zaktualizuj</button>
      <!-- Back Button -->
    <button onclick="history.back()" class="button">Powrót</button>     
</div>

    </form>

</div>

</body>
</html>
