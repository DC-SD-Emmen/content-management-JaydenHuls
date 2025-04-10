<?php
session_start();
require_once "classes/databaseConnection.php";
require_once "classes/UserManager.php";
require_once "classes/GameManager.php";
require_once "classes/GameDatabase.php";

//  als de user_id niet is ingesteld in de sessie, dan wordt de gebruiker doorgestuurd naar de loginpagina
if (!isset($_SESSION['user_id'])) {
    header("Location: loginpage.php");
    exit();
}

$db = new GameDatabase();
$gameManager = new GameManager($db);
$userId = $_SESSION['user_id'];

// Haal alle games op die in de wishlist staan
try {
    $stmt = $db->getConnection()->prepare("
        SELECT games.id, games.imagename 
        FROM games 
        JOIN user_games ON games.id = user_games.games_id
        WHERE user_games.user_id = :user_id
    ");
    $stmt->bindParam(":user_id", $userId);
    $stmt->execute();
    $wishlistGames = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Fout bij ophalen van wishlist: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mijn Wishlist</title>
    <link rel="stylesheet" href="CSS/wishlist.css">
</head>
<body>

<div class="wishlist-container">
    <h1>Mijn Wishlist</h1>

    <?php if (!empty($wishlistGames)) { ?>
        <div class="game-grid">
            <?php foreach ($wishlistGames as $games) { ?>
                <a href="Game_Details.php?game_id=<?php echo $games['id']; ?>" class="game-item">
                    <img src="uploads/<?php echo htmlspecialchars($games['imagename']); ?>" alt="Game Image">
                </a>
            <?php } ?>
        </div>
    <?php } else { ?>
        <p>Je hebt nog geen games in je wishlist.</p>
    <?php } ?>

    <br>
    <a href="index.php" class="back-button">Terug naar home</a>
</div>

</body>
</html>