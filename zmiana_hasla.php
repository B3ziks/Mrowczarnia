<?php
// zacznij sesję i zdobądź ID użytkownika
session_start();
$user_id = $_SESSION["id"];

// wróć na stronę logowania
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Połącz z bazą danych
$servername = "localhost";
$username = "Admin";
$password = "Cr7OHKpuVpkzWaR";
$dbname = "mrowczarnia";
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Sprawdź połaczenie
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


?>



<!DOCTYPE html>
<html>
<head>
  <title>Profile</title>
  <link rel="icon" type="image/x-icon" href="favicon.png">
<link rel="stylesheet" href="styleforms.css">

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


  <h2 style="text-align:center">Zmiana hasła</h2>
  <center><form method="post" action="change_password.php">
    <label for="oldPassword">Stare hasło:</label>
    <input type="password" name="oldPassword" id="oldPassword"><br><br>

    <label for="newPassword">Nowe hasło:</label>
    <input type="password" name="newPassword" id="newPassword"><br><br>

    <label  for="confirmPassword">Potwierdź hasło:</label>
    <input  type="password" name="confirmPassword" id="confirmPassword"><br><br>

    <input class="button"  type="submit" value="Zmień hasło">
  </form>
<?php
// Display change password messages if available
if (isset($_SESSION["change_password_error"])) {
    echo "<p>Error: " . $_SESSION["change_password_error"] . "</p>";
    unset($_SESSION["change_password_error"]);
}

if (isset($_SESSION["change_password_success"])) {
    echo "<p>Success: " . $_SESSION["change_password_success"] . "</p>";
unset($_SESSION["change_password_success"]);
}
?>


<br>
<br>

    <form action="profil.php" method="post">
    <input class="button" type="submit" value="Cofnij">
    </form>
</body>
</div>
</html>


	