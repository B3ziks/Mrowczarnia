<?php
session_start();

$userId = $_SESSION['id'];

$servername = "localhost";
$username = "Admin";
$password = "Cr7OHKpuVpkzWaR";
$dbname = "mrowczarnia";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && isset($_POST['name']) && isset($_POST['price'])) {
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete']) && isset($_POST['id'])) {
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payment_method'])) {
    $paymentMethod = $_POST['payment_method'];

    if ($paymentMethod === 'card') {
        $cardNumber = $_POST['card_number'];
        $expiryDate = $_POST['expiry_date'];
        $cwCode = $_POST['cw_code'];

        $orderDetails = array();
        $totalPrice = 0;

        foreach ($_SESSION['cart'] as $item) {
            $productName = $item['name'];
            $price = $item['price'];

            $productName = $conn->real_escape_string($productName);
            $price = $conn->real_escape_string($price);

            for ($i = 0; $i < $item['quantity']; $i++) {
                $orderDetails[] = $productName;
            }

            $totalPrice += ($price * $item['quantity']);
        }

        $orderDate = date('Y-m-d');

        $productNames = implode(', ', $orderDetails);

        $productNames = $conn->real_escape_string($productNames);
        $totalPrice = $conn->real_escape_string($totalPrice);

        $sql = "INSERT INTO orders (user_id, orderDate, productName, price, paymentMethod)
                VALUES ('$userId', '$orderDate', '$productNames', '$totalPrice', '$paymentMethod')";

        if ($conn->query($sql) === TRUE) {
            // Get the inserted order ID
            $orderID = $conn->insert_id;

            // Insert the products into the user_products table
            $insertValues = array();
            foreach ($orderDetails as $productName) {
                $productName = $conn->real_escape_string($productName);
                $insertValues[] = "('', '$userId', '0', '$productName')";
            }

            $insertQuery = "INSERT INTO user_products (id, user_id, productName, order_id)
                            VALUES " . implode(", ", $insertValues);

            if ($conn->query($insertQuery) === TRUE) {
                // Redirect to invoice.php with the order ID as a URL parameter
                header("Location: platnosc_sukces.php?order_id=" . $orderID);
                exit;
            } else {
                echo "Error: " . $insertQuery . "<br>" . $conn->error;
            }}else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
      
    } else {
        echo "Zła metoda!";
    }
     if ($paymentMethod === 'paypal') {


        $orderDetails = array();
        $totalPrice = 0;

        foreach ($_SESSION['cart'] as $item) {
            $productName = $item['name'];
            $price = $item['price'];

            // Escape special characters to prevent SQL injection
            $productName = $conn->real_escape_string($productName);
            $price = $conn->real_escape_string($price);

            for ($i = 0; $i < $item['quantity']; $i++) {
                $orderDetails[] = $productName;
            }

            $totalPrice += ($price * $item['quantity']);
        }

        $orderDate = date('Y-m-d');

        $productNames = implode(', ', $orderDetails);

        $productNames = $conn->real_escape_string($productNames);
        $totalPrice = $conn->real_escape_string($totalPrice);

        $sql = "INSERT INTO orders (user_id, orderDate, productName, price, paymentMethod)
                VALUES ('$userId', '$orderDate', '$productNames', '$totalPrice', '$paymentMethod')";

        if ($conn->query($sql) === TRUE) {
            // Get the inserted order ID
            $orderID = $conn->insert_id;

            $insertValues = array();
            foreach ($orderDetails as $productName) {
                $productName = $conn->real_escape_string($productName);
                $insertValues[] = "('', '$userId', '0', '$productName')";
            }

            $insertQuery = "INSERT INTO user_products (id, user_id, productName, order_id)
                            VALUES " . implode(", ", $insertValues);

            if ($conn->query($insertQuery) === TRUE) {
                // Redirect to invoice.php with the order ID as a URL parameter
                header("Location: platnosc_sukces.php?order_id=" . $orderID);
                exit;
            } else {
                echo "Error: " . $insertQuery . "<br>" . $conn->error;
            }
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Zła metoda!";
    }
if ($paymentMethod === 'blik') {


        $orderDetails = array();
        $totalPrice = 0;

        foreach ($_SESSION['cart'] as $item) {
            $productName = $item['name'];
            $price = $item['price'];

            $productName = $conn->real_escape_string($productName);
            $price = $conn->real_escape_string($price);

            for ($i = 0; $i < $item['quantity']; $i++) {
                $orderDetails[] = $productName;
            }

            $totalPrice += ($price * $item['quantity']);
        }

        $orderDate = date('Y-m-d');

        $productNames = implode(', ', $orderDetails);

        $productNames = $conn->real_escape_string($productNames);
        $totalPrice = $conn->real_escape_string($totalPrice);

        $sql = "INSERT INTO orders (user_id, orderDate, productName, price, paymentMethod)
                VALUES ('$userId', '$orderDate', '$productNames', '$totalPrice', '$paymentMethod')";

        if ($conn->query($sql) === TRUE) {
            // Get the inserted order ID
            $orderID = $conn->insert_id;

            $insertValues = array();
            foreach ($orderDetails as $productName) {
                $productName = $conn->real_escape_string($productName);
                $insertValues[] = "('', '$userId', '$productName', '$orderID')";
            }

            $insertQuery = "INSERT INTO user_products (id, user_id, productName, order_id)
                            VALUES " . implode(", ", $insertValues);

            if ($conn->query($insertQuery) === TRUE) {
                header("Location: platnosc_sukces.php?order_id=" . $orderID);
                exit;
            } else {
                echo "Error: " . $insertQuery . "<br>" . $conn->error;
            }
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        // Invalid 
        echo "Zła metoda!";
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mrówczarnia - Płatność</title>
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


    <h1>Płatność</h1>
    <form method="post">
        <?php
        if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
            echo "<h2>Podsumowanie</h2>";
            echo "<table>";
            echo "<tr><th>Nazwa</th><th>Cena</th><th>Ilość</th></tr>";
            $totalPrice = 0;
            foreach ($_SESSION['cart'] as $item) {
                echo "<tr>";
                echo "<td>" . $item['name'] . "</td>";
                echo "<td>" . $item['price'] . "</td>";
                echo "<td>" . $item['quantity'] . "</td>";
                echo "</tr>";
                $totalPrice += $item['price'] * $item['quantity'];
            }
            echo "</table>";
            echo "<p>Cena łącznie: " . $totalPrice . "</p>";

            echo "<label for='payment_method'>Metoda płatności:</label>";
            echo "<select id='payment_method' name='payment_method'>";
            echo '<option value="" selected disabled hidden>Wybierz</option>';
            echo "<option value='card'>Card</option>";
            echo "<option value='paypal'>PayPal</option>";
            echo "<option value='blik'>Blik</option>";
            echo "</select>";

            echo "<br><br><br>";
            // Display the additional fields based on the selected payment method
            echo "<div id='card_fields' class='hidden'>";
            echo "<label for='card_number'>Card Number:</label>";
            echo "<input type='text' id='card_number' name='card_number'>";
            echo "<label for='expiry_date'>Expiry Date:</label>";
            echo "<input type='text' id='expiry_date' name='expiry_date'>";
            echo "<label for='cw_code'>CW Code:</label>";
            echo "<input type='text' id='cw_code' name='cw_code'>";
            echo "</div>";

            echo "<div id='paypal_button' class='hidden'>";
            echo '<a href="https://www.paypal.com/signin?returnUri=https%3A%2F%2Fwww.paypal.com%2Fmyaccount%2Fautopay&state=%2Fconnect%2FB-216466662E3723024">
       <img src="https://koliber-dzieciom.pl/wp-content/uploads/2017/06/paypal-logo.png" alt="buttonpng" border="0" style=" width: 200px; height: 200px;"/>
</a>';
            echo "</div>";

            echo "<div id='blik_field' class='hidden'>";
            echo "<label for='blik_code'>Blik Code (6 digits):</label>";
            echo "<input type='text' id='blik_code' name='blik_code' maxlength='6'>";
            echo "</div>";

            echo "<br><br><button class='button' type='submit'>Zapłać</button>";
        } else {
            echo "<p>Koszyk jest pusty.</p>";
        }
        ?>
    </form>

<script src="platnosc.js"></script>

</body>
</html>