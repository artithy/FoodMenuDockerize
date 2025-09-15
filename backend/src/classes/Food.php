<?php

namespace App\classes;

use App\Database;
use PDO;

class Food extends Database
{
    public $pdo;

    public function __construct()
    {
        $this->pdo = $this->connect();
    }


    public function createTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS food(
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        cuisine_id INT NOT NULL,
        discount_price DECIMAL(10,2),
        vat_price DECIMAL(5,2),
        stock_quantity INT,
        status VARCHAR(50) DEFAULT 'active',
        image varchar(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

        return $this->pdo->exec($sql);
    }

    public function create($data)
    {
        $sql = "INSERT INTO food (name,description,price,cuisine_id, discount_price,vat_price,stock_quantity,status,image )
        VALUES(
        '{$data['name']}',
        '{$data['description']}',
        '{$data['price']}',
        '{$data['cuisine_id']}',
        '{$data['discount_price']}',
        '{$data['vat_price']}',
        '{$data['stock_quantity']}',
        '{$data['status']}',
        '{$data['image']}'
        )";

        return $this->pdo->exec($sql);
    }

    public function getAll()
    {
        $sql = "SELECT food.id, food.name,food.description,cuisine.name AS cuisine_name, food.price, food.discount_price,
        food.vat_price, food.stock_quantity, food.status, food.image
        FROM food
        LEFT JOIN cuisine ON food.cuisine_id = cuisine.id
        ORDER BY food.id DESC";

        return $this->pdo->query($sql)
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByID($id)
    {
        $sql = "SELECT food.*, cuisine.name AS cuisine_name
        FROM food
        LEFT JOIN cuisine ON food.cuisine_id = cuisine.id
        WHERE food.id = $id";

        return $this->pdo->query($sql)
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($id, $data)
    {
        $sql = "
        UPDATE food SET

        name =  '{$data['name']}',
        description = '{$data['description']}',
        price = '{$data['price']}',
        cuisine_id = '{$data['cuisine_id']}',
        discount_price = '{$data['discount_price']}',
        vat_price = '{$data['vat_price']}',
        stock_quantity = '{$data['stock_quantity']}',
        status = '{$data['status']}',
        image = '{$data['image']}'

        WHERE id = $id
        ";

        return $this->pdo->exec($sql);
    }

    public function delete($id)
    {
        return $this->pdo->exec("DELETE FROM food WHERE id = $id");
    }
}
