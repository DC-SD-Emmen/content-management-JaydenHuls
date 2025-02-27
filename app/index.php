<?php

$host = "mysql"; 
$dbname = "my-wonderful-website";
$charset = "utf8";
$port = "3306";
?>

<html>
<head>
    <title>Drenthe College docker web server</title>
</head>
<body>
    <form action="index.php" method="POST">
        <input type="text" name="Username" placeholder="Enter your Username">
        <input type="text" name="Password" placeholder="Enter your Password">
        <input type="submit">
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST["Username"];
        $password = $_POST["Password"];

        echo "Username" . htmlspecialchars($username) . "<br>";
        echo "Password" . htmlspecialchars($password) . ;
    }
    ?>
</body>
</html>
