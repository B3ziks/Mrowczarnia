<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== "admin") {
    header("location: login.php");
    exit;
}

$servername = "localhost";
$username = "Admin";
$password = "Cr7OHKpuVpkzWaR";
$dbname = "mrowczarnia";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (mysqli_connect_errno()) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id'])) {
    $product_id = $_POST["product_id"];
    $user_id = $_SESSION['id']; 

    $stmt = $conn->prepare("CALL delete_product(?, ?)");
    $stmt->bind_param("ii", $product_id, $user_id);
    $result = $stmt->execute();

    if ($result) {
        header("Location: index.php");
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }

    $stmt->close();
} else {
    header("Location: index.php");
    exit();
}

$conn->close();
?>
	