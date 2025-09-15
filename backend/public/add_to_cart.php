<?php
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/../vendor/autoload.php';

use App\classes\Cart;
use App\classes\CartItem;
use App\traits\AuthUtils;

$auth = new class {
    use AuthUtils;
};
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    $auth->printResponse([
        'status' => false,
        'message' => 'Only POST method allowed'
    ]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['cart_token'], $input['food_id'], $input['quantity'])) {
    http_response_code(400);
    $auth->printResponse([
        'status' => false,
        'message' => 'Cart token, food ID, and quantity are required'
    ]);
    exit;
}


$cart = new Cart();


$cart_item = new CartItem();
$cart_item->create_table();


$data = $cart->getCartByToken($input['cart_token']);

if (!$data) {
    $auth->printResponse([
        'status' => false,
        'message' => 'Cart not found'
    ]);
    exit;
}

$added = $cart_item->addItem($data['id'], $input['food_id'], $input['quantity']);


if ($added) {
    $auth->printResponse([
        'status' => true,
        'message' => 'Item added to cart successfully'
    ]);
} else {
    http_response_code(500);
    $auth->printResponse([
        'status' => false,
        'message' => 'Failed to add item to cart'
    ]);
}
