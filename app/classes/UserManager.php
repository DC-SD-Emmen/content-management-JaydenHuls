<?php
require_once "databaseConnection.php";

class UserManager {
    private $conn;

    public function __construct($database) {
        $this->conn = $database->getConnection();
    }

    public function register($username, $password) {
        try {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $this->conn->prepare("INSERT INTO Users (Username, Password) VALUES (:Username, :Password)");
            $stmt->execute([
                ':Username' => $username,
                ':Password' => $hashedPassword
            ]);

            return "Gebruiker succesvol geregistreerd!";
        } catch (PDOException $e) {
            return "Fout bij registreren: " . $e->getMessage();
        }
    }

    public function login($username, $password) {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM Users WHERE Username = :Username");
            $stmt->execute([':Username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['Password'])) {
                session_start();
                $_SESSION['Username'] = $user['Username'];
                $_SESSION['user_id'] = $user['id'];
                header("Location: http://localhost/index.php");
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
            // Controleer of de game al in de wishlist staat
            $stmt = $this->conn->prepare("SELECT * FROM user_games WHERE user_id = :user_id AND games_id = :games_id");
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':games_id', $gamesID);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return "Deze game staat al in je wishlist.";
            }

            // Voeg de game toe aan de wishlist
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
            // Verwijder de game uit de wishlist
            $stmt = $this->conn->prepare("DELETE FROM user_games WHERE user_id = :user_id AND games_id = :games_id");
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':games_id', $gamesID);
            $stmt->execute();

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
?>
