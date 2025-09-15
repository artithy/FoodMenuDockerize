<?php


namespace App\classes;

use App\Database;
use App\traits\AuthUtils;
use PDO;


class Cuisine extends Database
{
    use AuthUtils;
    public $pdo;

    public function __construct()
    {
        $db = new Database();
        $this->pdo = $db->connect();
    }

    public function createTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS cuisine (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL
        )";

        return $this->pdo->exec($sql);
    }

    public function create($name)
    {
        return $this->pdo->exec("INSERT INTO cuisine (name) VALUES ('$name')");
    }

    public function getAll()
    {
        return $this->pdo->query("SELECT * FROM cuisine ORDER BY id DESC")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($id, $name)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM cuisine WHERE name = :name AND id != :id");
        $stmt->execute([':name' => $name, ':id' => $id]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            return false;
        }

        $stmt = $this->pdo->prepare("UPDATE cuisine SET name = :name WHERE id = :id");
        return $stmt->execute([':name' => $name, ':id' => $id]);
    }

    public function delete($id)
    {
        return $this->pdo->exec("DELETE FROM cuisine WHERE id = $id");
    }

    public function getCuisineNameById($id)
    {
        return $this->pdo->query("SELECT name FROM cuisine WHERE id=$id")
            ->fetch(PDO::FETCH_ASSOC);
    }
}
