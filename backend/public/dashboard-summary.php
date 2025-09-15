<?php
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/../vendor/autoload.php';

use App\classes\Order;

header('Content-Type: application/json');

$order = new order();

$data = [
    'todays_orders' => (int)$order->countTodayOrders(),
    'pending_delivery' => (int)$order->countPendingDelivery(),
    'total_payments' => (float)$order->sumTodayPayments(),
    'total_completed_orders' => (int)$order->countCompleteOrders(),
];

echo json_encode($data);
