<?php
session_start();


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['name'], $_POST['price'], $_POST['quantity'])) {
    $productId = $_POST['id'];
    $productName = $_POST['name'];
    $productPrice = $_POST['price'];
    $quantity = intval($_POST['quantity']); // Get the quantity from the form

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }

    $productIndex = array_search($productId, array_column($_SESSION['cart'], 'id'));

    if ($productIndex !== false) {
        $_SESSION['cart'][$productIndex]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][] = array(
            'id' => $productId,
            'name' => $productName,
            'price' => $productPrice,
            'quantity' => $quantity
        );
    }

    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}
$servername = "localhost";
$username = "Admin";
$password = "Cr7OHKpuVpkzWaR";
$dbname = "mrowczarnia";
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
function getAvailableQuantity($conn, $productId) {
    $stmt = $conn->prepare("SELECT quantity FROM produkty WHERE id = :id");
    $stmt->execute(['id' => $productId]);
    $product = $stmt->fetch();
    return $product ? $product['quantity'] : 0;
}

// dodawanie do koszyka
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $productId = $_POST['id'];
    $productName = $_POST['name'];
    $productPrice = $_POST['price'];

    $availableQuantity = getAvailableQuantity($conn, $productId);
    
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }

    $productIndex = array_search($productId, array_column($_SESSION['cart'], 'id'));
    $currentQuantityInCart = $productIndex !== false ? $_SESSION['cart'][$productIndex]['quantity'] : 0;

    if ($currentQuantityInCart < $availableQuantity) {
        if ($productIndex !== false) {
            $_SESSION['cart'][$productIndex]['quantity'] += 1;
        } else {
            $_SESSION['cart'][] = array(
                'id' => $productId,
                'name' => $productName,
                'price' => $productPrice,
                'quantity' => 1
            );
        }
    } else {
        // Set error message if not enough stock
        $_SESSION['error_message'] = "Nie możesz dodać więcej produktów, niż jest dostępnych obecnie w sklepie.";
    }
    
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// usuwanie z koszyka
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $productId = $_POST['id'];

    if (isset($_SESSION['cart'])) {
        $productIndex = array_search($productId, array_column($_SESSION['cart'], 'id'));
        if ($productIndex !== false) {
            $_SESSION['cart'][$productIndex]['quantity'] -= 1;
            if ($_SESSION['cart'][$productIndex]['quantity'] <= 0) {
                unset($_SESSION['cart'][$productIndex]);
                $_SESSION['cart'] = array_values($_SESSION['cart']); // Re-index the array
            }
        }
    }
    
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Mrówczarnia - Koszyk</title>
  <link rel="icon" type="image/x-icon" href="favicon.png">
  <link rel="stylesheet" href="styleplatnosc.css">

</head>
<body>
<div class="header">
  <a href="index.php"><img src="mrowkaTitle.png" alt="Home"></a>
  <h1 class="shop-name">Mrówczarnia</h1>
  <div style="flex-grow: 1;"></div>
</div>

    <div class="navbar">
        <a href="index.php">Strona Główna<img src="anticon.png" alt="Home"></a>
        <a href="koszyk.php">Koszyk <img src="koszyk.png" alt="Koszyk"></a>
        <a href="profil.php">Konto <img src="anthead.png" alt="Konto"></a>
    </div>


    <div class="basket-container">
        <h2>Twój koszyk</h2>

   <!--  error message -->
    <?php if (isset($_SESSION['error_message'])): ?>
        <p class="error"><?php echo $_SESSION['error_message']; ?></p>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>
       <?php
if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
    echo "<table class='cart-table'>";
    echo "<tr><th>Nazwa Produktu</th><th>Cena</th><th>Ilość</th><th>Akcje</th></tr>";
    $totalPrice = 0;
    foreach ($_SESSION['cart'] as $item) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($item['name']) . "</td>";
        echo "<td>" . htmlspecialchars($item['price']) . "</td>";
        echo "<td>" . htmlspecialchars($item['quantity']) . "</td>";
        echo "<td>";
        echo "<form method='post' style='display: inline;'>";
        echo "<input type='hidden' name='id' value='" . $item['id'] . "'>";
        echo "<input type='hidden' name='name' value='" . $item['name'] . "'>";
        echo "<input type='hidden' name='price' value='" . $item['price'] . "'>";
        echo "<button  type='submit' name='add'>+</button>";
        echo "</form> ";
        echo "<form method='post' style='display: inline;'>";
        echo "<input type='hidden' name='id' value='" . $item['id'] . "'>";
        echo "<button  type='submit' name='delete'>-</button>";
        echo "</form>";
        echo "</td>";
        echo "</tr>";
        $totalPrice += $item['price'] * $item['quantity'];
    }
    echo "</table>";

            echo "<div class='cart-summary'>";
            echo "<p>Całkowity Koszt: " . $totalPrice . " zł</p>";
echo "</div>";
            
            echo '<form action="platnosc.php" method="post">
                <input class="button" type="submit" value="Przejdź do płatności">
            </form>';
        } else {
            echo "<p>Twój koszyk jest pusty.</p>";
        }
        ?>
        <form action="index.php" method="post">
            <input class="button" type="submit" value="Powrót do przeglądania">
        </form>
    </div>
</body>
</html>	