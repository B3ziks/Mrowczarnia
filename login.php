<!DOCTYPE html>
<html>
<head>
<?php
session_start();
?>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="styleforms.css">

<title>Mrówczarnia - Logowanie</title>
  <link rel="icon" type="image/x-icon" href="favicon.png">
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

<center>
<div class="box">
<h2>Logowanie</h2>
<form action="login.php" method="post">
<div class="form-group">
    <br><br>
    <label for="login"><p>Login</p></label><br>
    <input type="text" class="form-control" id="username" placeholder="Wpisz login" name="username" required><br>
</div>

<div class="form-group">
    <label for="password"><p>Hasło</p></label><br>
    <input type="password" class="form-control" id="password" placeholder="Wpisz hasło" name="password" required><br>
</div>

     <input class="button" type="submit" value="Zaloguj">
     

</form>
    <div class="social-media">
        <img src="facebook.png" alt="Facebook" onclick="loginWithFacebook()">
        <img src="twitter.png" alt="Twitter" onclick="loginWithTwitter()">
        <img src="google.png" alt="Google" onclick="loginWithGoogle()">
    </div>

    <div class="footer">
        <h2>Nie masz konta?</h2>
		<br>
        <a class="button" href="registration.php">Zarejestruj się!</a>
    </div>
      <br>
    <form action="index.php" method="post">
    <input class="button" type="submit" value="Cofnij">
    </form>

<?php


$servername = "localhost";
$username = "Admin";
$password = "Cr7OHKpuVpkzWaR";
$dbname = "mrowczarnia";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST["username"];
  $password = $_POST["password"];

  // Pobierz dane użytkownika z bazy danych
  $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

if (password_verify($password, $row["password"])) {
    echo "Pomyślnie zalogowano jako " . $row["username"];
    $_SESSION["loggedin"] = true;
    $_SESSION["username"] = $username;
    $_SESSION["email"] = $row["email"];  // Corrected line
    $_SESSION["id"] = $row["id"];        // Corrected line
    $_SESSION["role"] = $row["role"];    // Corrected line
    header("Location: index.php");
    exit;
    } else {
      echo "Nieprawidłowe hasło";
    }
  } else {
    echo "Nie znaleziono użytkownika o podanym loginie";
  }

  $stmt->close();
  $conn->close();
}

?>

</div>
</center>
</body>
</html>				