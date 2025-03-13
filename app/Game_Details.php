<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./style.css">
</head>
<body>


<?php

spl_autoload_register(function ($className) {
    require_once 'classes/' . $className . '.php';
  });

// Haal de game ID uit de URL
$gameID = isset($_GET['game_id']) ? (int)$_GET['game_id'] : null;

// Maak een instantie van GameManager
$db = new GameDatabase();
$gameManager = new GameManager($db);

if ($gameID !== null) {
    $games = $gameManager->fetch_game_id($gameID);

    if (!empty($games)) {
        //html specials is om te filteren (XSS cross site scripting attack tegen gaan)
        $game = $games[0];  // Er wordt maar één game opgehaald, dus pak het eerste item
        echo "<img src='uploads/" . $game->getImageName() . "'><br>";
        echo "Title: " . htmlspecialchars($game->getTitle()) . "<br>";
        echo "Genre: " . htmlspecialchars($game->getGenre()) . "<br>";
        echo "Platform: " . htmlspecialchars($game->getPlatform()) . "<br>";
        echo "Release Year: " . htmlspecialchars($game->getReleaseYear()) . "<br>";
        echo "Rating: " . htmlspecialchars($game->getRating()) . "<br>";
    } else {
        echo "Game not found.";
    }
} else {
    echo "Invalid game ID.";
}

    echo "<div id='terug-balk2'>";
    echo "<a href='index.php'><div id='Back-Button'>BACK</div></a>";
    echo "</div>";

?>
    
</body>
</html>