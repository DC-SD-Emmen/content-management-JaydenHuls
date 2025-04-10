<?php

class GameManager {

    private $conn;

    function __construct(GameDatabase $db) {
        $this->conn = $db->getConnection();
    }

    public function addNewGameToDB($title, $genre, $platform, $releaseYear, float $rating, $imageName) {
        try {
            // Bereid de query voor met named placeholders
            $query = "INSERT INTO games (title, genre, platform, release_year, rating, imageName)
                      VALUES (:title, :genre, :platform, :releaseYear, :rating, :imageName)";
    
            // Bereid de statement voor
            $stmt = $this->conn->prepare($query);
    
            // Bind de parameters aan de statement
            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt->bindParam(':genre', $genre, PDO::PARAM_STR);
            $stmt->bindParam(':platform', $platform, PDO::PARAM_STR);
            $stmt->bindParam(':releaseYear', $releaseYear, PDO::PARAM_STR);
            $stmt->bindParam(':rating', $rating, PDO::PARAM_STR);
            $stmt->bindParam(':imageName', $imageName, PDO::PARAM_STR);
    
            // Voer de statement uit
            if ($stmt->execute()) {
                echo "Game succesvol toegevoegd.";
            } else {
                echo "Er is iets misgegaan.";
            }
        } catch (PDOException $e) {
            // Vang eventuele fouten op en toon het bericht
            echo "Fout: " . $e->getMessage();
        }
    }

    public function getGameFromDB() {
        //we willen een array vullen met games
        //hier maken we eerst een lege array
        $games = [];
        
        try {
            // Bereid de query voor
            $query = "SELECT * FROM games";
            
            // Voer de query uit met PDO
            //prepare is klaarzetten
            $stmt = $this->conn->prepare($query);
            //execute is uitvoeren
            $stmt->execute();
            
            // Haal alle resultaten op
            if ($stmt->rowCount() > 0) {
                //PDO fetch_assoc haalt de resultaten op als een associatieve array
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $game = new Game($row);
                    $games[] = $game;
                }
            } else {
                echo "Geen resultaten gevonden.";
            }
        } catch (PDOException $e) {
            // Verwerk de uitzondering
            echo "Fout: " . $e->getMessage();
        }
        
        return $games;
    }


      /**
       * First thing first, you should make another function inside off the GameMananger.php file.
       * This function should be called getGameTitleFromDB(). In that function, you make a query similar
       * to the one inside off getGameFromDB(). But they key difference is, that you only grab the title and id.
       * Then inside Index.php, you call that function
       * and make an input or hidden input with the id and the title. Once you click on the title,
       * you should be redirected to a page you still have to make. Called Game_Details.php 
       * Once you redirect to that page, you should put an id in the url, while redirecting. 
       * Once there, you should use a $_GET['id'], so you can get that number from the url.
       * Now that you've done that, you should change the getGameFromDB() so it accepts a 
       * variable. -> getGameFromDB($id). You should also change the query and simply make it
       * SELECT title, genre, platform, release_year, rating FROM gamelibrary WHERE id = '$id'
       * So you only get that specific row. Once you've done that, simply load it inside the GameDetails page or whatever and you should be fine.
       */
    public function getGameTitleFromDB() {
        $query = "SELECT id, title FROM games";
    }

    public function fetch_game_id($gameID) {
        $games = [];
        
        try {
            // Bereid de query voor met een named placeholder voor het game-ID
            $query = "SELECT * FROM games WHERE id = :id";
            $stmt = $this->conn->prepare($query);
    
            // Bind het game-ID aan de placeholder
            $stmt->bindParam(':id', $gameID, PDO::PARAM_INT);
    
            // Voer de statement uit
            $stmt->execute();
    
            // Haal resultaten op en maak Game-objecten
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $game = new Game($row);
                $games[] = $game;
            }
            
        } catch (PDOException $e) {
            // Verwerk eventuele fouten
            echo "Fout: " . $e->getMessage();
        }
    
        return $games;
    }

    public function fileUpload($file) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($file["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Controleer of het bestandspad niet leeg is
        if (empty($file["tmp_name"])) {
            echo "Geen bestand geüpload.";
            return false;
        }

        // Controleer of het bestand een afbeelding is
        $check = getimagesize($file["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            echo "Bestand is geen afbeelding.";
            $uploadOk = 0;
        }

        // Controleer of het bestand al bestaat
        if (file_exists($target_file)) {
            echo "Sorry, bestand bestaat al.";
            $uploadOk = 0;
        }

        // Controleer de bestandsgrootte
        if ($file["size"] > 500000) {
            echo "Sorry, je bestand is te groot.";
            $uploadOk = 0;
        }

        // Sta alleen bepaalde bestandstypen toe
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            echo "Sorry, alleen JPG, JPEG, PNG & GIF bestanden zijn toegestaan.";
            $uploadOk = 0;
        }

        // Controleer of $uploadOk is ingesteld op 0 door een fout
        if ($uploadOk == 0) {
            echo "Sorry, je bestand is niet geüpload.";
            return false;
        } else {
            // Probeer het bestand te uploaden
            if (move_uploaded_file($file["tmp_name"], $target_file)) {
                echo "Het bestand " . htmlspecialchars(basename($file["name"])) . " is geüpload.";
                return true;
            } else {
                echo "Sorry, er is een fout opgetreden bij het uploaden van je bestand.";
                return false;
            }
        }
    }

    public function deleteGame($gameID) {
        try {
            // Verwijder eerst gerelateerde records uit de user_games-tabel
            $query1 = "DELETE FROM user_games WHERE games_id = :id";
            $stmt1 = $this->conn->prepare($query1);
            $stmt1->bindParam(':id', $gameID, PDO::PARAM_INT);
            $stmt1->execute();

            // Verwijder daarna de game zelf
            $query2 = "DELETE FROM games WHERE id = :id";
            $stmt2 = $this->conn->prepare($query2);
            $stmt2->bindParam(':id', $gameID, PDO::PARAM_INT);

            if ($stmt2->execute()) {
                return "Game succesvol verwijderd.";
            } else {
                return "Verwijderen van de game is mislukt.";
            }
        } catch (PDOException $e) {
            return "Fout: " . $e->getMessage();
        }
    }
}
?>