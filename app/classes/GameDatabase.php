<?php

class GameDatabase {

    private $servername = "mysql";
    private $username = "root";
    private $password = "root";
    private $dbname = "User_login";
    private $conn;

    //construct is een functie die direct afgaat, op het moment dat je een nieuw Database object aanmaakt
    public function __construct()
    {
        try {
            $this->conn = new PDO("mysql:host=$this->servername;dbname=$this->dbname", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo "connection failed: " . $e->getMessage();
        }
    }

    public function getConnection()
    {
        return $this->conn;
    }
}