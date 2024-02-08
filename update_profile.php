<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php");
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

$user_id = $_SESSION["id"];

if (isset($_FILES["profile_picture"]) && $_FILES["profile_picture"]["error"] === UPLOAD_ERR_OK) {
    // Read the file content
    $imageData = file_get_contents($_FILES["profile_picture"]["tmp_name"]);

    // Update the user's profile picture in the database
    $query = "UPDATE users SET profile_picture = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("bi", $imageData, $user_id);
    $stmt->send_long_data(0, $imageData);
    $stmt->execute();
    $stmt->close();

    header("Location: profil.php");
    exit;
} else {
    echo "No file was uploaded.";
    exit;
}
?>