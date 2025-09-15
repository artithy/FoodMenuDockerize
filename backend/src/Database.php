<?php

namespace App;

use PDO;
use PDOException;

class Database
{
    private $host_name = "db";
    private $dbname = "foodmenu";
    private $username = "user";
    private $password = "userpassword";
    public $pdo;

    public function connect()
    {
        try {
            $this->pdo = new PDO(
                "mysql:host={$this->host_name};port=3306;dbname={$this->dbname}",
                $this->username,
                $this->password
            );

            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->exec("SET time_zone = '+06:00'");
            return $this->pdo;
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }
}
