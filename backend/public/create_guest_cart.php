<?php
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/../vendor/autoload.php';

use App\classes\Cart;
use App\traits\AuthUtils;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    $this->printResponse([
        'status' => false,
        'message' => 'Only POST method allowed'
    ]);
    exit;
}

$cart = new Cart();
$cart->create_table();
$token = $cart->createCart();

if ($token) {
    echo json_encode([
        'status' => true,
        'cart_token' => $token
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'status' => false,
        'message' => 'Cart creation failed'
    ]);
}
