<?php


  class GameManager {

    private $conn;

      function __construct(GameDatabase $db) {
        $this->conn = $db->getConnection();
      }

      public function addNewGameToDB($title, $genre, $platform, $releaseYear, float $rating, $imageName) {
        try {
            // Prepare the query with named placeholders
            $query = "INSERT INTO games (title, genre, platform, release_year, rating, imageName)
                      VALUES (:title, :genre, :platform, :releaseYear, :rating, :imageName)";
    
            // Prepare the statement
            $stmt = $this->conn->prepare($query);
    
            // Bind the parameters to the statement
            //Bind paramater gebruiken we om SQL injectie te voorkomen (veilig programeren)
            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt->bindParam(':genre', $genre, PDO::PARAM_STR);
            $stmt->bindParam(':platform', $platform, PDO::PARAM_STR);
            $stmt->bindParam(':releaseYear', $releaseYear, PDO::PARAM_STR);
            $stmt->bindParam(':rating', $rating, PDO::PARAM_STR); // Using PDO::PARAM_STR for float as a string
            $stmt->bindParam(':imageName', $imageName, PDO::PARAM_STR);
    
            // Execute the statement
            if ($stmt->execute()) {
                echo "Game added successfully.";
            } else {
                echo "Something went wrong.";
            }
        } catch (PDOException $e) {
            // Catch any errors and display the message
            echo "Error: " . $e->getMessage();
        }
    }
    

      public function getGameFromDB() {
        $games = [];
        
        try {
            // Prepare the query
            $query = "SELECT * FROM games";
            
            // Execute the query using PDO
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            // Fetch all the results
            if ($stmt->rowCount() > 0) {
                //fetch assoc, alle informatie wordt gefetched. in de vorm van een associatieve array
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $game = new Game($row);
                    $games[] = $game;  // Use shorthand for array_push
                }
            } else {
                echo "0 results";
            }
        } catch (PDOException $e) {
            // Handle exception
            echo "Error: " . $e->getMessage();
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
        $games = [];  // Initialize the array to store games
        
        try {
            // Prepare the query with a named placeholder for the game ID
            $query = "SELECT * FROM games WHERE id = :id";
            $stmt = $this->conn->prepare($query);
    
            // Bind the gameID to the placeholder using bindParam
            $stmt->bindParam(':id', $gameID, PDO::PARAM_INT);
    
            // Execute the statement
            $stmt->execute();
    
            // Fetch results and create Game objects
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $game = new Game($row);
                $games[] = $game;
            }
            
        } catch (PDOException $e) {
            // Handle any errors that occur during the execution
            echo "Error: " . $e->getMessage();
        }
    
        // Return the array of Game objects (not just a single game)
        return $games;
    }

    public function fileUpload($file) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($file["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
       
        $check = getimagesize($file["tmp_name"]);
        if($check !== false) {
            // echo "File is an image - " . $check["mime"] . ".";
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }


        // Check if file already exists
        if (file_exists($target_file)) {
            echo "Sorry, file already exists.";
            $uploadOk = 0;
        }

        // Check file size
        if ($file["size"] > 500000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }



        // Allow certain file formats
        if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
        && $imageFileType != "gif" ) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
            // if everything is ok, try to upload file
        } else {
            if (move_uploaded_file($file["tmp_name"], $target_file)) {
                // echo "The file ". htmlspecialchars( basename( $file["name"])). " has been uploaded.";
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    }
    
  }
?> 