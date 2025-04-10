<?php
class User {
    private $conn;

    // De constructor krijgt de databaseverbinding via dependency injection
    public function __construct(Database $database) {
        $this->conn = $database->getConnection();
    }

    // Haal gebruiker op via ID
    public function getUser($user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM Users WHERE id = :id");
        $stmt->bindParam(':id', $user_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Werk de gebruikersnaam en het wachtwoord bij
    public function updateUser($user_id, $new_username, $new_password, $current_password) {
        // Controleer of het huidige wachtwoord klopt
        $stmt = $this->conn->prepare("SELECT password FROM Users WHERE id = :id");
        $stmt->bindParam(':id', $user_id);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (password_verify($current_password, $user['password'])) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $this->conn->prepare("UPDATE Users SET username = :username, password = :password WHERE id = :id");
            $stmt->bindParam(':username', $new_username);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':id', $user_id);
            $stmt->execute();
            return "Gegevens succesvol bijgewerkt!";
        } else {
            return "Huidig wachtwoord is incorrect!";
        }
    }

    // Verwijder gebruiker uit de database
    public function deleteUser($user_id) {
        try {
            // Verwijder gerelateerde records uit andere tabellen
            $query1 = "DELETE FROM user_games WHERE user_id = :id";
            $stmt1 = $this->conn->prepare($query1);
            $stmt1->bindParam(':id', $user_id, PDO::PARAM_INT);
            $stmt1->execute();

            // Verwijder de gebruiker zelf
            $query2 = "DELETE FROM Users WHERE id = :id";
            $stmt2 = $this->conn->prepare($query2);
            $stmt2->bindParam(':id', $user_id, PDO::PARAM_INT);

            if ($stmt2->execute()) {
                return true; // Gebruiker succesvol verwijderd
            } else {
                return false; // Verwijderen mislukt
            }
        } catch (PDOException $e) {
            return false; // Fout bij verwijderen
        }
    }
}
?>
