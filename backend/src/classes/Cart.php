<?php

namespace App\classes;

use App\Database;
use App\traits\AuthUtils;
use PDO;

class Cart extends Database
{
    use AuthUtils;
    public $pdo;
    private $tokenHandler;

    public function __construct()
    {
        $this->pdo = $this->connect();
        $this->tokenHandler = new Token();
    }


    public function create_table()
    {
        $sql = "CREATE TABLE IF NOT EXISTS carts (
            id INT AUTO_INCREMENT PRIMARY KEY,
            cart_token VARCHAR(255) NOT NULL UNIQUE,
            user_id INT DEFAULT NULL,
            status VARCHAR(50) DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";

        return $this->pdo->exec($sql);
    }

    public function createCart()
    {
        $token = $this->tokenHandler->generateGuestCartToken();

        $stmt = $this->pdo->prepare("INSERT into carts (cart_token, status) VALUES ('$token', 'active')");
        $stmt->execute();

        return $token;
    }

    public function getCartByToken($token)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM carts WHERE cart_token = '$token' LIMIT 1");
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
