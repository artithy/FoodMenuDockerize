<?php
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/../vendor/autoload.php';

use App\classes\Order;

header('Content-Type: application/json');

$order = new Order();

echo json_encode($order->ordersByDays());
