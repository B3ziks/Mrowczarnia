<?php
session_start();
$user_id = $_SESSION["id"];

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

$servername = "localhost";
$username = "Admin";
$password = "Cr7OHKpuVpkzWaR";
$dbname = "mrowczarnia";
$conn = mysqli_connect($servername, $username, $password, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$query = "SELECT username, email, profile_picture FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($username, $email, $profile_picture);
$stmt->fetch();

if ($stmt->num_rows > 0) {
    $stmt->fetch();
} else {
    echo "No results found.";
}
?>


<!DOCTYPE html>
<html>
<head>
  <title>Mrówczarnia - Profil</title>
    
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Permanent+Marker&display=swap" rel="stylesheet">
  <link rel="icon" type="image/x-icon" href="favicon.png">
<link rel="stylesheet" href="styleprofil.css">

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
        <a href="login.php">Konto <img src="anthead.png" alt="Konto"></a>
    </div>

<div class="account-info">
  <h1>Profil</h1><br>
  <div>
    <h3>
    <a class="button" href="logout.php">Wyloguj</a>
<br><br><br>
      Login: <?php echo $username; ?><br><br>
      E-mail: <?php echo $email; ?><br><br>
    </h3>

    <?php
    // Check if profile picture is available
    if (!empty($profile_picture)) {
        echo "<img src='data:image/jpeg;base64," . base64_encode($profile_picture) . "' alt='Profile Picture' style='width: 300px; height: 300px;' />";
    } else {
        echo "<h4></h4><br>";
    }
    ?>

    <a class="button" style="margin-bottom:10px;" href="index.php">&#x2B05; Powrót do strony głównej</a>
    <a class="button" style="margin-bottom:10px;"  href="zmiana_hasla.php">Zmiana Hasła</a>


<div class="orders-table">
    <h2>Moje Zamówienia</h2>
    <table>
        <tr>
            <th>Order ID</th>
            <th>Produkty</th>
            <th>Ilość</th>
        </tr>
        <?php
        if (isset($_SESSION['id'])) {
            $user_id = $_SESSION['id'];

            // Fetch orders from the database for the logged-in user
            $query = "SELECT order_id, productName, COUNT(*) as quantity FROM user_products WHERE user_id = ? GROUP BY order_id, productName ORDER BY order_id";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            $orders = [];
            while ($row = $result->fetch_assoc()) {
                $orders[$row['order_id']][] = $row;
            }

            foreach ($orders as $order_id => $products) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($order_id) . "</td>";
                echo "<td>";
                foreach ($products as $product) {
                    echo htmlspecialchars($product['productName']) . "<br>";
                }
                echo "</td>";
                echo "<td>";
                foreach ($products as $product) {
                    echo htmlspecialchars($product['quantity']) . "<br>";
                }
                echo "</td>";
                echo "</tr>";
            }

            $stmt->close();
        } else {
            echo "<tr><td colspan='3'>Proszę się zalogować, aby zobaczyć zamówienia.</td></tr>";
        }
        ?>
    </table>
</div>

</center>
</body>
</div>
</html>







