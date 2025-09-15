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
error_log(print_r($input, true));


$cart_token = $input['cart_token'] ?? null;
if (!$cart_token) {
    http_response_code(400);
    $auth->printResponse([
        'status' => false,
        'message' => 'Cart Token is required',
    ]);
    exit;
}


$cart = new Cart();
$cart = $cart->getCartByToken($cart_token);

if (!$cart) {
    http_response_code(404);
    $auth->printResponse([
        'status' => false,
        'message' => 'Cart not found',
    ]);
    exit;
}


if (!isset($input['food_id'])) {
    http_response_code(400);
    $auth->printResponse([
        'status' => false,
        'message' => 'Food ID is required',
    ]);
    exit;
}

$cartItem = new CartItem();
$deleted = $cartItem->delete($cart['id'], $input['food_id']);

if ($deleted) {
    http_response_code(200);
    $auth->printResponse([
        'status' => true,
        'message' => 'Cart item deleted successfully',
    ]);
} else {
    http_response_code(404);
    $auth->printResponse([
        'status' => false,
        'message' => 'Cart item not found or already deleted',
    ]);
}
