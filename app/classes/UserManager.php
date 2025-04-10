<?php
ob_start(); // Start output buffering, zodat de uitvoer naar de browser wordt gebufferd


require_once "databaseConnection.php"; // Zorg ervoor dat er geen uitvoer vóór deze regel is

class UserManager {
    private $conn;

    public function __construct($database) {
        $this->conn = $database->getConnection();
    }

    public function register($username, $password) {


        //first check if the username already exists
        //use try and catch to handle exceptions
        try {
            $stmt = $this->conn->prepare("SELECT * FROM Users WHERE Username = :Username");
            $stmt->execute([':Username' => $username]);
            if ($stmt->rowCount() > 0) {
                return "Deze gebruikersnaam is al in gebruik.";
            }
        } catch (PDOException $e) {
            return "Fout bij controleren gebruikersnaam: " . $e->getMessage();
        }

        // password_hash zorgt ervoor dat de wachtwoord hash wordt gemaakt waardoor het niet als platte tekst in de database komt te staan
        // en dat de wachtwoord hash ook veilig is
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            // voegt een nieuwe gebruiker toe aan de database
            $stmt = $this->conn->prepare("INSERT INTO Users (Username, Password) VALUES (:Username, :Password)");
            // dit zijn prepared statements, dit zorgt ervoor dat de gegevens veilig zijn en dat er geen SQL injectie kan plaatsvinden
            // de :Username en :Password zijn placeholders die later worden vervangen door de waarden van de variabelen $username en $hashedPassword    
            // de username wordt in de query geplaatst en de hashedpassword in de database
            $stmt->execute([
                ':Username' => $username,
                ':Password' => $hashedPassword
            ]);

            //reroute to loginpage.php
            header("Location: http://localhost/Loginpage.php"); // Headers moeten hier nog niet worden verzonden
            exit(); // Stop de uitvoering van het script na de header-omleiding
            // fout bij catch returned een bericht dat er iets fout is gegaan bij het registreren
        } catch (PDOException $e) {
            return "Fout bij registreren: " . $e->getMessage();
        }
    }

    // start de login functie met de username en password variables
    public function login($username, $password) {
        // gebruik try en catch om fouten af te handelen
        // gebruik prepared statements om SQL injectie te voorkomen
        // gebruik fetch om de gegevens van de gebruiker op te halen uit de database
        try {
            $stmt = $this->conn->prepare("SELECT * FROM Users WHERE Username = :Username");
            $stmt->execute([':Username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            // het eerste wat de if statement doet is controleren of de gebruiker bestaat
            // als de gebruiker bestaat, dan wordt er gecontroleerd of het ingevoerde wachtwoord overeenkomt met het wachtwoord in de database met de password_verify functie
            // als het wachtwoord overeenkomt, dan wordt de gebruiker ingelogd en worden de gegevens opgeslagen in de sessie
            // als het wachtwoord niet overeenkomt, dan wordt er een foutmelding weergegeven
            if ($user && password_verify($password, $user['Password'])) {
                // dit zijn sessie variabelen die ervoor zorgen dat de gebruiker ingelogd blijft
                // de sessie variabelen worden opgeslagen in de $_SESSION superglobal array
                $_SESSION['Username'] = $user['Username'];
                $_SESSION['user_id'] = $user['id'];
                header("Location: http://localhost/index.php"); // Headers moeten hier nog niet worden verzonden
                exit();
            } else {
                return "Gebruikersnaam of wachtwoord is onjuist.";
            }
        } catch (PDOException $e) {
            return "Fout bij inloggen: " . $e->getMessage();
        }
    }

    public function addToWishlist($gamesID) {
        $userId = $_SESSION['user_id'];
        try {
            // de geprepareerde query zoekt in de user_games tabel of er al een rij bestaat met de opgegeven user_id en games_id
            // de bindparam zorgt ervoor dat de waarden veilig zijn en dat er geen SQL injectie kan plaatsvinden
            // de execute functie voert de query uit
            $stmt = $this->conn->prepare("SELECT * FROM user_games WHERE user_id = :user_id AND games_id = :games_id");
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':games_id', $gamesID);
            $stmt->execute();
            // de rowCount function geeft aan hoeveel rijen de SELECT query heeft opgeleverd
            // als er meer dan 0 rijen zijn, dan betekent dit dat de game al in de wishlist staat
            if ($stmt->rowCount() > 0) {
                return "Deze game staat al in je wishlist.";
            }

            // als er nog geen rij is gevonden met de opgegeven user_id en games_id, dan wordt er een nieuwe rij toegevoegd aan de user_games tabel
            // de bindparam zorgt ervoor dat de waarden veilig zijn en dat er geen SQL injectie kan plaatsvinden
            $stmt = $this->conn->prepare("INSERT INTO user_games (user_id, games_id) VALUES (:user_id, :games_id)");
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':games_id', $gamesID);
            $stmt->execute();
            return "Game succesvol toegevoegd aan wishlist!";
        } catch (PDOException $e) {
            return "Fout bij toevoegen aan wishlist: " . $e->getMessage();
        }
    }

    public function removeFromWishlist($gamesID) {
        $userId = $_SESSION['user_id'];
        try {
            // de delete-query verwijdert de rij uit de user_games tabel waar de opgegeven user_id en games_id overeenkomen
            // de bindparam zorgt ervoor dat de waarden veilig zijn en dat er geen SQL injectie kan plaatsvinden
            // de execute functie voert de query uit
            $stmt = $this->conn->prepare("DELETE FROM user_games WHERE user_id = :user_id AND games_id = :games_id");
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':games_id', $gamesID);
            $stmt->execute();
            // als de rowCount 0 rijen vindt wanneer je de games hebt verwijderd uit de wishlist
            // dan returned hij de eerste return als de game niet in de wishlist staat en je probeert de game te verwijderen dan laat het de tweede return zien
            if ($stmt->rowCount() > 0) {
                return "Game succesvol verwijderd uit wishlist!";
            } else {
                return "Game niet gevonden in wishlist.";
            }
        } catch (PDOException $e) {
            return "Fout bij verwijderen uit wishlist: " . $e->getMessage();
        }
    }
}

ob_end_flush(); // Stuur de output naar de browser
?>
