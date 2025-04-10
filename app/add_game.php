<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style.css">
</head>
<body>
    
</body>
</html>
<?php

spl_autoload_register(function ($className) {
    require_once 'classes/' . $className . '.php';
});

$db = new GameDatabase();
$GameManager = new GameManager($db);

echo "<div id='terug-balk'>";
echo "<a href='index.php'><div id='Back-Button'>BACK</div></a>";
echo "</div>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $genre = $_POST['genre'];
    $platform = $_POST['platform'];
    $releaseYear = $_POST['release_year'];
    $rating = (float)$_POST['rating'];
    $file = $_FILES['image'];

    // Controleer of er een bestand is geüpload
    if (isset($file) && $file['error'] == 0) {
        $gameManager = new GameManager($db);

        // Upload het bestand
        if ($gameManager->fileUpload($file)) {
            // Voeg de game toe aan de database
            $imageName = basename($file["name"]);
            $gameManager->addNewGameToDB($title, $genre, $platform, $releaseYear, $rating, $imageName);
        } else {
            echo "File upload failed.";
        }
    } else {
        echo "No file was uploaded or there was an error.";
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
    <title>Voeg een game toe</title>
</head>
<body>
    <h1>Voeg een nieuw spel toe</h1>

    <form id="add-game-form" action="" method="POST" enctype="multipart/form-data">
        <label for="title">Naam van het spel:</label>
        <input type="text" id="title" name="title" required><br><br>

        <label for="genre">Genre:</label>
        <input type="text" id="genre" name="genre" required><br><br>

        <label for="platform">Platform:</label>
        <input type="text" id="platform" name="platform" required><br><br>

        <label for="release_year">Releasejaar:</label>
        <input type="date" id="release_year" name="release_year" required><br><br>

        <label for="rating">Rating:</label>
        <input type="text" id="rating" name="rating" required><br><br>

        <input type="file" name="image" id="fileToUpload"><br><br>

        <input type="submit" name="submit" value="Game toevoegen">
    </form>
</body>
</html>