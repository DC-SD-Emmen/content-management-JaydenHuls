<?php
require_once "databaseConnection.php";

class UserManager {
    private $conn;

    public function __construct(Database $database) {
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
                return "Succesvol ingelogd!";
            } else {
                return "Gebruikersnaam of wachtwoord is onjuist.";
            }
        } catch (PDOException $e) {
            return "Fout bij inloggen: " . $e->getMessage();
        }
    }
}
?>
