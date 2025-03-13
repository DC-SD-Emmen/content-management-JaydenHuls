<?php

require_once "classes/databaseConnection.php";
require_once "classes/UserManager.php";

$database = new Database();
$userManager = new UserManager($database);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drenthe College Docker Web Server</title>
</head>
<body>
    <form action="" method="POST">
        <input type="text" name="Username" placeholder="Enter your Username" required>
        <input type="password" name="Password" placeholder="Enter your Password" required>
        <input type="submit" name="submit" value="Sign Up">
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
        $username = htmlspecialchars($_POST["Username"]);
        $password = $_POST["Password"]; // Wachtwoord hoeft niet door htmlspecialchars()

        if (!empty($username) && !empty($password)) {
            echo $userManager->register($username, $password);
        }
    }
    ?>
</body>
</html>
