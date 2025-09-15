<?php

namespace App\classes;

use App\Database;
use PDO;

class OrderItem extends Database
{
    public $pdo;

    public function __construct()
    {
        // try {
        //     $this->pdo = $this->connect();
        //     if ($this->pdo) {
        //         echo "DB Connected successfully";
        //     }
        // } catch (\PDOException $e) {
        //     echo "DB Connection failed: " . $e->getMessage();
        // }

        $this->pdo = $this->connect();
    }

    public function createTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS order_items(
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        food_id INT NOT NULL,
        food_name VARCHAR(255) NOT NULL,
        quantity INT NOT NULL,
        price_at_order DECIMAL(10,2) NOT NULL,
        image VARCHAR(255),
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
    )";

        return $this->pdo->exec($sql);
    }

    public function addItems($order_id, $items)
    {
        foreach ($items as $item) {
            $sql = "INSERT INTO order_items(order_id, food_id, food_name, quantity, price_at_order, image) VALUES(
            $order_id,
            {$item['food_id']},
            '{$item['food_name']}',
            {$item['quantity']},
            {$item['price']},
            '" . ($item['image'] ?? '') . "'
        )";

            $this->pdo->exec($sql);
        }

        return true;
    }

    public function getItemsByOrderId($order_id)
    {
        $sql = "SELECT * FROM order_items WHERE order_id = {$order_id}";
        return $this->pdo->query($sql)
            ->fetchAll(PDO::FETCH_ASSOC);
    }
}
