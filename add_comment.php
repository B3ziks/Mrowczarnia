<head>
<style>
  body, html {
            background-color: #434750;
            padding: 4% 15% 8% 15%;
            color: grey;
        }

        .container {
      display: flex;
      flex-direction: column;
      background-color: #2e3133;
      padding: 24px;
      border-radius: 16px;
      margin: 0 auto;
      text-align: center;
      color:#9669be;
    }
    .btn {
      max-width: 260px !important;
      margin: 0 auto;
	 background-color:#b58adb; 
	 color:#27fefb;
	 border-color:#b58adb;
	 
    }
		
		
	
</style>
<?php

session_start();

// Database credentials
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'Admin');
define('DB_PASSWORD', 'Cr7OHKpuVpkzWaR');
define('DB_NAME', 'mrowczarnia');


$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($link === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    echo "User not logged in";
    header("location: login.php");
    exit;
}

$content = "";
$content_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["content"]))) {
        $content_err = "Please enter your comment.";
    } else {
        $content = trim($_POST["content"]);
    }

    if (empty($content_err)) {
    $sql = "CALL add_comment(?, ?, ?)";

        if ($stmt = mysqli_prepare($link, $sql)) {
            mysqli_stmt_bind_param($stmt, "isi", $_SESSION['id'], $content, $_POST['product_id']);

            if (mysqli_stmt_execute($stmt)) {
                // Redirect to the product details page
                header("Location: product_details.php?id=" . $_POST['product_id']);
                exit;
            } else {
                echo "Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    mysqli_close($link);

}
?>
