<?php
session_start();

$gameID = isset($_GET['game_id']) ? (int)$_GET['game_id'] : null;
spl_autoload_register(function ($className) {
    require_once 'classes/' . $className . '.php';
});

$db = new GameDatabase();
$gameManager = new GameManager($db);
$userManager = new UserManager($db);

// Als er op "Add to Wishlist" is geklikt
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add-to-wishlist'])) {
    $gameID = $_POST['game_id'];
    $message = $userManager->addToWishlist($gameID);

    // Voeg de game toe aan de sessie-wishlist
    $_SESSION['wishlist'][$gameID] = true;
}

// Als er op "Remove from Wishlist" is geklikt
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['remove-from-wishlist'])) {
    $gameID = $_POST['game_id'];
    $message = $userManager->removeFromWishlist($gameID); 
    // Verwijder de game uit de sessie-wishlist
    unset($_SESSION['wishlist'][$gameID]);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Details</title>
    <link rel="stylesheet" href="./style.css">
</head>
<body>

<?php
if ($gameID !== null) {
    $games = $gameManager->fetch_game_id($gameID);

    if (!empty($games)) {
        $game = $games[0];
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

// Controleer of de game in de wishlist zit
if (isset($_SESSION['wishlist'][$gameID])) {
?>
    <form method="POST">
        <input type="hidden" name="game_id" value="<?php echo $gameID; ?>">
        <input type="submit" name="remove-from-wishlist" value="Remove from Wishlist">
    </form>   
<?php
} else {
?>
    <form method="POST">
        <input type="hidden" name="game_id" value="<?php echo $gameID; ?>">
        <input type="submit" name="add-to-wishlist" value="Add to Wishlist">
    </form>   
<?php
}

// Toon het bericht ONDERAAN de pagina
if (!empty($message)) {
    echo "<div id='message'>$message</div>";
}
?>


</body>
</html>
