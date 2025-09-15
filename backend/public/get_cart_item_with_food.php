<?php
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/../vendor/autoload.php';

use App\classes\Cart;
use App\classes\CartItem;
use App\traits\AuthUtils;

$auth = new class {
    use AuthUtils;
};

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    $auth->printResponse([
        'status' => 'error',
        'message' => 'Only GET method allowed'
    ]);
}

if (!isset($_GET['cart_token']) || empty($_GET['cart_token'])) {
    http_response_code(400);
    $auth->printResponse([
        'status' => 'error',
        'message' => 'Cart Token is required',
    ]);
}

$cart = new Cart();
$cartItem = new CartItem();

$data = $cart->getCartByToken($_GET['cart_token']);

if (!$data) {
    http_response_code(404);
    $auth->printResponse([
        'status' => 'false',
        'message' => 'Cart not found'
    ]);
}

$items = $cartItem->getItemWithFood($data['id']);





$auth->printResponse([
    'status' => true,
    'item' => $items
]);
