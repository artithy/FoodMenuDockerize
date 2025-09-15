<?php

namespace App\classes;

use App\Database;
use PDO;
use PDOException;

class Token extends Database
{
    public $pdo;

    public function __construct()
    {
        // PDO connection ta ekhane assign korlam jate sob method e use korte pari
        $this->pdo = $this->connect();
    }

    public function createTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS token (
            id INT AUTO_INCREMENT PRIMARY KEY,
            token VARCHAR(255) NOT NULL,
            user_id INT NOT NULL,
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        return $this->pdo->exec($sql);
    }

    public function create($data)
    {
        $sql = "INSERT INTO token (token, user_id, is_active) VALUES (:token, :user_id, 1)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':token' => $data['token'],
            ':user_id' => $data['user_id']
        ]);
    }

    public function validate($token)
    {
        $sql = "SELECT * FROM token WHERE token = :token AND is_active = 1 LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':token' => $token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deactivate($token)
    {
        $sql = "UPDATE token SET is_active = 0 WHERE token = :token";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([':token' => $token]);
    }

    public function getUserIdByToken($token)
    {
        $sql = "SELECT user_id FROM token WHERE token = :token AND is_active = 1 LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':token' => $token]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['user_id'] ?? false;
    }

    public function generateGuestCartToken()
    {
        return uniqid('guestcart_', true);
    }
}
