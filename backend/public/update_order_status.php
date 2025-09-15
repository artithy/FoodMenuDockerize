<?php
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/../vendor/autoload.php';

use App\classes\Order;
use App\traits\AuthUtils;

$order = new Order();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    $order->printResponse([
        'status' => false,
        'message' => 'Only POST method allowed'
    ]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['order_id'], $input['status'])) {
    http_response_code(400);
    $order->printResponse([
        'status' => false,
        'message' => 'Order ID and status are required'
    ]);
    exit;
}

$order = new Order();
$data = $order->updateStatus(intval($input['order_id']), $input['status']);

if ($data) {
    $order->printResponse([
        'status' => true,
        'message' => 'Order status updated successfully'
    ]);
} else {
    http_response_code(500);
    $order->printResponse([
        'status' => false,
        'message' => 'Failed to update order status'
    ]);
}
