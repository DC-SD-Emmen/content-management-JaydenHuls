<?php
class Database {
    private $host = "mysql";
    private $dbname = "User_login";
    private $username = "root";
    private $password = "root";
    private $conn;

    public function __construct() {
        try {
            $this->conn = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Databaseverbinding mislukt: " . $e->getMessage());
        }
    }

    public function getConnection() {
        return $this->conn;
    }
}
?>