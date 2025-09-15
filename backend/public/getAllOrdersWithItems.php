<?php
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/../vendor/autoload.php';

use App\classes\Order;
use App\classes\OrderItem;

$order = new Order();
$orderItem = new OrderItem();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => false, 'message' => 'Only GET allowed']);
    exit;
}

$orders = $order->getAllOrdersWithItems();

foreach ($orders as &$o) {
    $o['order_details'] = $orderItem->getItemsByOrderId($o['id']);
}

echo json_encode([
    'status' => true,
    'orders' => $orders
]);
