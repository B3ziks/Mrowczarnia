<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Rejestracja</title>
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
    <div class="container">
	<div class="box">

      <h2 style="text-align:center">Zarejestruj się</h2><br>
      <form method="POST" action="registration.php">

        <div class="form-group">

          <input type="text" placeholder="login" name="username" id="username" required>
        </div>

        <div class="form-group">
          <input type="password" placeholder="hasło" id="password" name="password" required>
        </div>

        <div class="form-group">

          <input type="email" placeholder="email" name="email" id="email" required>
        </div>
        <br>

        <input class="button" type="submit" value="Dołącz!">
      </form>
      <br>
    <form action="login.php" method="post">
    <input  class="button" type="submit" value="Cofnij">
    </form>


<?php
  $servername = "localhost";
  $username = "Admin";
  $password = "Cr7OHKpuVpkzWaR";
  $dbname = "mrowczarnia";

  $conn = new mysqli($servername, $username, $password, $dbname);


  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = $_POST["password"];


$password_regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[\S]{8,}$/';
    if (!preg_match($password_regex, $password)) {
      echo "Hasło musi mieć co najmniej 8 znaków i zawierać co najmniej jedną małą literę, jedną wielką literę i jedną cyfrę.";
      exit;
    }
    $email_regex = '/^(([^<>()[\]\\.,;:\s@"\']+(\.[^<>()[\]\\.,;:\s@"\']+)*)|("[^"\']+"))@((\[\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\])|(([a-zA-Z\d\-]+\.)+[a-zA-Z]{2,}))$/';
    if (!preg_match($email_regex, $email)) {
      echo "Nieprawidłowy adres email";
      exit;
    }


    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
    echo "Użytkownik o podanym loginie już istnieje.";
    exit;
    }

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
    echo "Ten adres email jest już zajęty.";
    exit;
    }


    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "SELECT MIN(t1.id + 1) AS next_id
        FROM users AS t1
        LEFT JOIN users AS t2 ON t1.id + 1 = t2.id
        WHERE t2.id IS NULL";
     $result = $conn->query($sql);
     $row = $result->fetch_assoc();
     $next_id = $row["next_id"];

     $stmt = $conn->prepare("INSERT INTO users (id, username, email, password) VALUES (?, ?, ?, ?)");
     $stmt->bind_param("isss", $next_id, $username, $email, $hashed_password);

   if ($stmt->execute()) {
      echo "Użytkownik został pomyślnie zarejestrowany";
   } else {
    echo "Error: " . $stmt->error;
   }

   $stmt->close();
   $conn->close();
  }
?>
    </div>
  </div>
   </div>
 
</body>
</html>								