<?php

namespace App\classes;

use App\Database;
use App\traits\AuthUtils;
use PDO;

class Order extends Database
{
    public $pdo;
    use AuthUtils;

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
        $sql = "CREATE TABLE IF NOT EXISTS orders(
        id INT AUTO_INCREMENT PRIMARY KEY,
        cart_id INT NOT NULL,
        customer_name VARCHAR(255) NOT NULL,
        customer_address TEXT NOT NULL,
        customer_phone VARCHAR(20) NOT NULL,
        order_notes TEXT,
        total_price DECIMAL(10,2) NOT NULL,
        payment_status VARCHAR(50) DEFAULT 'pending',
        status VARCHAR(50) DEFAULT 'pending',  
        order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        order_id VARCHAR(50) UNIQUE NOT NULL,
        invoice_id VARCHAR(50) DEFAULT NULL,
        FOREIGN KEY(cart_id) REFERENCES carts(id) ON DELETE CASCADE
        )";

        return $this->pdo->exec($sql);
    }

    public function createOrder($cart_id, $customer_name, $customer_address, $customer_phone, $order_notes, $total_price, $order_id, $invoice_id = null)
    {
        $invoice_id_quoted = $invoice_id === null ? 'NULL' : $this->pdo->quote($invoice_id);

        $sql = "INSERT INTO orders (
    cart_id, customer_name, customer_address, customer_phone, order_notes, total_price, order_id, invoice_id, status
) VALUES (
    {$this->pdo->quote($cart_id)},
    {$this->pdo->quote($customer_name)},
    {$this->pdo->quote($customer_address)},
    {$this->pdo->quote($customer_phone)},
    {$this->pdo->quote($order_notes)},
    {$this->pdo->quote($total_price)},
    {$this->pdo->quote($order_id)},
    $invoice_id_quoted,
    'pending'
)";




        $this->pdo->exec($sql);
        return $this->pdo->lastInsertId();
    }


    public function getAllOrdersWithItems()
    {

        $sql = "SELECT id, order_id, invoice_id, customer_name, customer_address, customer_phone, total_price, status, order_date
            FROM orders
            ORDER BY id DESC";
        $orders = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);


        foreach ($orders as &$order) {
            $sqlItems = "SELECT food_id, food_name, quantity, price_at_order, image
                     FROM order_items
                     WHERE order_id = " . $order['id'];
            $order['order_details'] = $this->pdo->query($sqlItems)->fetchAll(PDO::FETCH_ASSOC);
        }

        return $orders;
    }



    public function getById($id)
    {
        $sql = "SELECT* FROM orders WHERE id = $id";

        return $this->pdo->query($sql)
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update($data, $id)
    {
        $fields = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = " . ($value === null ? "NULL" : $this->pdo->quote($value));
        }

        $sql = "UPDATE orders SET " . implode(", ", $fields) . " WHERE id = $id";

        return $this->pdo->exec($sql);
    }

    public function updateStatus($id, $status)
    {
        $valid_statuses = ['pending', 'shipped', 'delivered', 'cancelled', 'returned'];

        if (!in_array($status, $valid_statuses)) {
            throw new \Exception("Invalid status: $status");
        }

        $sql = "UPDATE orders SET status = '{$status}' WHERE id = $id";
        return $this->pdo->exec($sql);
    }



    // public function delete($id)
    // {
    //     $sql = "DELETE FROM orders WHERE id = $id";
    //     return $this->pdo->exec($sql);
    // }


    public function countTodayOrders()
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as total FROM orders WHERE DATE(order_date) = CURDATE()");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }

    public function countPendingDelivery()
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as total FROM orders WHERE status = 'pending'");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function sumTodayPayments()
    {
        $stmt = $this->pdo->prepare("SELECT SUM(total_price) as total FROM orders WHERE DATE(order_date) = CURDATE()");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }

    public function countCompleteOrders()
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as total FROM orders WHERE status = 'delivered'");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function ordersByDays()
    {
        $stmt = $this->pdo->query("
        SELECT DATE(order_date) as day, COUNT(*) as total from orders
        WHERE order_date >= CURDATE() - INTERVAL 6 DAY
        GROUP BY DATE(order_date);
        ");

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $labels = [];
        $data = [];
        foreach ($result as $row) {
            $labels[] = date('D', strtotime($row['day']));
            $data[] = $row['total'];
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    public function ordersByHour()
    {
        $stmt = $this->pdo->query("
        SELECT HOUR(order_date) as hour, COUNT(*) as total from orders 
        WHERE DATE(order_date) = CURDATE()
        GROUP BY HOUR(order_date)
        ");

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $labels = [];
        $data = [];
        foreach ($result as $row) {
            $labels[] = $row['hour'] . ":00";
            $data[] = intval($row['total']);
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }
}
