<?php
session_start();

if (!isset($_SERVER['HTTP_REFERER']) || !isset($_GET['order_id'])) {
    header("Location: platnosc.php");
    exit;
}
$servername = "localhost";
$username = "Admin";
$password = "Cr7OHKpuVpkzWaR";
$dbname = "mrowczarnia";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Mrówczarnia - Sukces</title>
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


    <div class="container">

<?php
$game_name = urldecode($_GET['game_name']);
echo "<h1>Płatność udana" . $game_name . "</h1>";

if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
    echo "<h2>Potwierdzenie płatności</h2>";
    echo "<table>";
    echo "<tr><th>Nazwa</th><th>Cena</th><th>Ilość</th><th>Cena łącznie</th><th>Metoda płatności</th></tr>";
    $totalPrice = 0;
    foreach ($_SESSION['cart'] as $item) {
        echo "<tr>";
        echo "<td>" . $item['name'] . "</td>";
        echo "<td>" . $item['price'] . "</td>";
        echo "<td>" . $item['quantity'] . "</td>";
        $totalPrice += $item['price'] * $item['quantity'];
        echo "<td>" . $totalPrice . "</td>";
$orderID = $_GET['order_id'];
         $sql = "SELECT paymentMethod FROM orders WHERE id = '$orderID'";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $paymentMethod = $row['paymentMethod'];
            echo "<td>" . $paymentMethod . "</td>";
        } else {
            echo "<td>Payment Method Not Found</td>";
        }

        echo "</tr>";
    }
    echo "</table>";
}
?>



<a href="faktura.php?order_id=<?php echo $_GET['order_id']; ?>" target="_blank" class="button">Przejdz do faktury</a>



<a href="index.php" class="button">Powrót na stronę główną</a>

    </div>
</body>
</html>