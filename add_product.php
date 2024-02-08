<?php
// establish database connection
$servername = "localhost";
$username = "Admin";
$password = "Cr7OHKpuVpkzWaR";
$dbname = "mrowczarnia";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if (mysqli_connect_errno()) {
    die("Connection failed: " . mysqli_connect_error());
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = $_POST["name"];
  $description = $_POST["description"];
  $price = $_POST["price"];
  $sale_price = $_POST["sale_price"];
  $quantity = $_POST["quantity"];

  $photo = $_FILES["photo"];
  $photo_name = $photo["name"];
  $photo_tmp_name = $photo["tmp_name"];
  $photo_size = $photo["size"];
  $photo_error = $photo["error"];

  $photo_ext = strtolower(pathinfo($photo_name, PATHINFO_EXTENSION));
  $allowed_exts = ["jpg", "jpeg", "png", "gif"];

   if (in_array($photo_ext, $allowed_exts)) {
    if ($photo_error === 0) {
      if ($photo_size <= 5000000) {
        $photo_data = file_get_contents($photo_tmp_name);

    $stmt = $conn->prepare("CALL add_product(?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssddi", $name, $photo_data, $description, $price, $sale_price, $quantity);
        $stmt->execute();
        $stmt->close();

        header("Location: index.php");
        exit();
      } else {
        echo "Sorry, your file is too large.";
      }
    } else {
      echo "Sorry, there was an error uploading your file.";
    }
  } else {
    echo "Sorry, only JPG, JPEG, PNG, and GIF files are allowed.";
  }
}

$conn->close();

?>