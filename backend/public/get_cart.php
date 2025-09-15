<?php
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/../vendor/autoload.php';

use App\classes\Cart;

header('Content-Type: application/json');


if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    $this->printResponse([
        'status' => false,
        'message' => 'Only GET method allowed'
    ]);
    exit;
}

if (!isset($_GET['cart_token']) || empty($_GET['cart_token'])) {
    http_response_code(400);
    $this->printResponse([
        'status' => false,
        'message' => 'Cart token is required'
    ]);
    exit;
}

$cartToken = $_GET['cart_token'];

$cart = new Cart();

$data = $cart->getCartByToken($cartToken);

if ($data) {
    echo json_encode([
        'status' => true,
        'cart' => $data
    ]);
} else {
    http_response_code(404);
    echo json_encode([
        'status' => false,
        'message' => 'Cart not found'
    ]);
}
