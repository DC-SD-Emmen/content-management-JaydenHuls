<?php
session_start();

$gameID = isset($_GET['game_id']) ? (int)$_GET['game_id'] : null;
spl_autoload_register(function ($className) {
    require_once 'classes/' . $className . '.php';
});

$db = new GameDatabase();
$gameManager = new GameManager($db);
$userManager = new UserManager($db);

// Als er op "Toevoegen aan Verlanglijst" is geklikt
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add-to-wishlist'])) {
    $gameID = $_POST['game_id'];
    $message = $userManager->addToWishlist($gameID);

    // Voeg de game toe aan de sessie-verlanglijst
    $_SESSION['wishlist'][$gameID] = true;
}

// Als er op "Verwijderen uit Verlanglijst" is geklikt
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['remove-from-wishlist'])) {
    $gameID = $_POST['game_id'];
    $message = $userManager->removeFromWishlist($gameID); 
    // Verwijder de game uit de sessie-verlanglijst
    unset($_SESSION['wishlist'][$gameID]);
}

// Als er op "Game Verwijderen" is geklikt
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete-game'])) {
    $gameID = $_POST['game_id'];
    $message = $gameManager->deleteGame($gameID);

    // Verwijder de game ook uit de sessie-verlanglijst als deze erin zit
    unset($_SESSION['wishlist'][$gameID]);
}

?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Details</title>
    <link rel="stylesheet" href="CSS/style.css">
</head>
<body>

<?php
// Haal de game op en toon de details
if ($gameID !== null) {
    $games = $gameManager->fetch_game_id($gameID);

    if (!empty($games)) {
        $game = $games[0];
        echo "<div id='game-details'>"; 
        echo "<img src='uploads/" . $game->getImageName() . "'><br>";
        echo "<h2>" . htmlspecialchars($game->getTitle()) . "</h2>";
        echo "<p>Genre: " . htmlspecialchars($game->getGenre()) . "</p>";
        echo "<p>Platform: " . htmlspecialchars($game->getPlatform()) . "</p>";
        echo "<p>Uitgavejaar: " . htmlspecialchars($game->getReleaseYear()) . "</p>";
        echo "<p>Beoordeling: " . htmlspecialchars($game->getRating()) . "</p>";
        echo "</div>";
    } else {
        echo "<div id='game-details'><p>Game niet gevonden.</p></div>";
    }
} else {
    echo "<div id='game-details'><p>Ongeldig game-ID.</p></div>";
}

echo "<div id='terug-balk2'>";
echo "<a href='index.php'><div id='Back-Button'>TERUG</div></a>";
echo "</div>";

// Controleer of de game in de verlanglijst zit
if (isset($_SESSION['wishlist'][$gameID])) {
?>
    <form id="remove-wishlist-form" method="POST">
        <input type="hidden" name="game_id" value="<?php echo $gameID; ?>">
        <input type="submit" name="remove-from-wishlist" value="Verwijderen uit wishlist">
    </form>   
<?php
} else {
?>
    <form id="add-wishlist-form" method="POST">
        <input type="hidden" name="game_id" value="<?php echo $gameID; ?>">
        <input type="submit" name="add-to-wishlist" value="Toevoegen aan wishlist">
    </form>   
<?php
}

// Voeg een verwijder-knop toe
if ($gameID !== null) {
?>
    <form id="delete-game-form" method="POST">
        <input type="hidden" name="game_id" value="<?php echo $gameID; ?>">
        <input type="submit" name="delete-game" value="Game Verwijderen" onclick="return confirm('Weet je zeker dat je deze game wilt verwijderen?');">
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
