<?php
session_start();

if (!isset($_SESSION['Username'])) {
    header("Location: loginpage.php");
    exit();
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['Logout'])) {
        session_unset();
        session_destroy();
        header("Location: loginpage.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>My Website</title>
    <link rel="stylesheet" href="CSS/style.css">
    <link rel="icon" href="./favicon.ico" type="image/x-icon">
  </head>
  <body>

  <?php
  spl_autoload_register(function ($className) {
      include 'classes/' . $className . '.php';
  });

  $gameID = isset($_GET['game_id']) ? $_GET['game_id'] : '';

  $db = new GameDatabase();
  $GameManager = new GameManager($db);

  $games = $GameManager->getGameFromDB();
  $GameManager->getGameTitleFromDB();

  echo "<div id='menu-balk'>";
    echo "<a href='add_game.php'><div id='add-game'>ADD GAME</div></a>";
    echo "<a href='wishlist.php'><div id='wishlist'>WISHLIST</div></a>";
    echo "<a href='account.php'><div id='account'>ACCOUNT</div></a>"; 
    echo "<form id='logout-form' method='POST'>";
      echo "<button type='submit' name='Logout' id='logout-button'>LOGOUT</button>";
    echo "</form>";
  echo "</div>";

  echo "<div id='allgames'>";
    foreach ($games as $game) {
        echo '<a href="game_details.php?game_id=' . $game->getId() . '">';
        echo "<img src='uploads/" . htmlspecialchars($game->getImageName()) . "'>";
        echo "</a>";
    }
  echo "</div>";
  ?>
  </body>
</html>