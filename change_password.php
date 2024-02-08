<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $oldPassword = $_POST["oldPassword"];
    $newPassword = $_POST["newPassword"];
    $confirmPassword = $_POST["confirmPassword"];


    if ($newPassword !== $confirmPassword) {
        $_SESSION["change_password_error"] = "Pole nowe hasło i pole potwierdź hasło nie zgadzają się.";
        header("Location: zmiana_hasla.php");
        exit;
    }

    $password_regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/';
    if (!preg_match($password_regex, $newPassword)) {
        $_SESSION["change_password_error"] = "Hasło musi mieć co najmniej 8 znaków i zawierać co najmniej jedną małą literę, jedną wielką literę i jedną cyfrę.";
        header("Location: zmiana_hasla.php");
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


    $query = "SELECT password FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($hashedPassword);
        $stmt->fetch();

        if (password_verify($oldPassword, $hashedPassword)) {
            // Hash the new password
            $newHashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            $updateQuery = "UPDATE users SET password = ? WHERE id = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->bind_param("si", $newHashedPassword, $user_id);
            $updateStmt->execute();
            $updateStmt->close();

            $_SESSION["change_password_success"] = "Hasło zostało zmienione pomyślnie";
            header("Location: zmiana_hasla.php");
            exit;
        } else {
            $_SESSION["change_password_error"] = "Podane aktualne hasło jest nieprawidłowe.";
            header("Location: zmiana_hasla.php");
            exit;
        }
    } else {
        $_SESSION["change_password_error"] = "Nie znaleziono użytkownika.";
        header("Location: zmiana_hasla.php");
        exit;
    }
}

header("Location: zmiana_hasla.php");
exit;
?>