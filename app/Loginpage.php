<?php

require_once "classes/databaseConnection.php";
require_once "classes/UserManager.php";
$database = new Database();
$userManager = new UserManager($database);

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $username = htmlspecialchars($_POST["Username"]);
    $password = $_POST["Password"]; // Geen `htmlspecialchars()` nodig voor wachtwoord

    if (!empty($username) && !empty($password)) {
        echo $userManager->login($username, $password);
    }
}

?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="CSS/style.css">
    <link rel="icon" href="./favicon.ico" type="image/x-icon">
    <title>Drenthe College Docker Web Server</title>
</head>
<body>
    <form id="login-form" action="" method="POST">
        <input type="text" name="Username" placeholder="Enter your Username" required>
        <input type="password" name="Password" placeholder="Enter your Password" required>
        <input type="submit" name="submit" value="Login">
    </form>

    <?php

    ?>
</body>
</html>
