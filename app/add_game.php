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



    if (isset($_POST["submit"])) {

        $title = $_POST['title'] ?? '';
        $genre = $_POST['genre'] ?? '';
        $platform = $_POST['platform'] ?? '';
        $release_year = $_POST['release_year'];
        $rating = $_POST['rating'] ?? '';
    
        // Simpele validatie
        if (empty($title) || empty($genre) || empty($platform) || empty($release_year) || empty($rating)) {
            echo "All fields must be filled";
            return;
        }
    
        if (!is_numeric($rating) || $rating < 0 && $rating > 10) {
            echo "The rating must be between 0 - 10";
            return;
        }

        $GameManager->fileUpload($_FILES['image']);

        $GameManager->addNewGameToDB($title, $genre, $platform, $release_year, $rating, $_FILES['image']['name']);

    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voeg een game toe</title>
</head>
<body>
    <h1>Voeg een nieuw spel toe</h1>

    <form action="" method="POST" enctype="multipart/form-data">
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