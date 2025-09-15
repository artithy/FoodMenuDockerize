<?php

namespace App\classes;

use App\Database;
use App\traits\AuthUtils;
use PDO;

class CartItem extends Database
{
    use AuthUtils;

    public function __construct()
    {
        $this->pdo = $this->connect();
    }

    public function create_table()
    {
        $sql = "CREATE TABLE IF NOT EXISTS cart_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            cart_id INT NOT NULL,
            food_id INT NOT NULL,
            quantity INT NOT NULL,
            price DECIMAL(10,2) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (cart_id) REFERENCES carts(id) ON DELETE CASCADE,
            FOREIGN KEY (food_id) REFERENCES food(id) ON DELETE CASCADE
        )";

        return $this->pdo->exec($sql);
    }

    public function addItem($cart_id, $food_id, $quantity)
    {
        $sql = "INSERT INTO cart_items(cart_id, food_id, quantity) VALUES ('$cart_id', '$food_id', '$quantity')";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute();
    }

    public function updateItem($cart_id, $food_id, $newQuantity)
    {
        $sql = "SELECT * FROM cart_items WHERE cart_id = '$cart_id' AND food_id = '$food_id' LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($item) {
            $updateSql = "UPDATE cart_items SET quantity = '$newQuantity' WHERE id = '{$item['id']}'";
            $updateStmt = $this->pdo->prepare($updateSql);
            return $updateStmt->execute();
        }

        return false;
    }


    public function getItemWithFood($cart_id)
    {
        $sql = "SELECT 
        cart_items.id AS cart_item_id,
        cart_items.quantity,
        food.id AS food_id,
        food.name,
        food.price,
        food.discount_price,
        food.vat_price, 
        food.image
        FROM cart_items
        LEFT JOIN food ON cart_items.food_id = food.id
        WHERE cart_items.cart_id = '$cart_id'  
        AND cart_items.quantity > 0";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function delete($cart_id, $food_id)
    {
        $sql = "DELETE FROM cart_items WHERE cart_id = $cart_id AND food_id = $food_id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute();
    }
}
