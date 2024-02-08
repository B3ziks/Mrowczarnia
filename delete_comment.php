<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true || $_SESSION["role"] !== "admin") {
    header("location: login.php");
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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['comment_id'])) {
    $comment_id = $_POST['comment_id'];
    error_log("Comment ID: " . $comment_id);

    // Set the current user's ID before calling the procedure
    $currentUser = $_SESSION['id']; // Replace with the actual session variable or method to get the logged-in user's ID
    $conn->query("SET @currentUser = {$currentUser}");

    // Call the stored procedure
    $stmt = $conn->prepare("CALL delete_comment(?)");
    $stmt->bind_param("i", $comment_id);
    $result = $stmt->execute();

    if ($result) {
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    } else {
        echo "Error deleting comment: " . $conn->error;
    }

    $stmt->close();
} else {
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}

$conn->close();
?>
