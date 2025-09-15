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
        'status' => 'error',
        'message' => 'Only POST method allowed'
    ]);
}

$input = json_decode(file_get_contents('php://input'), true);

if (!isset($_GET['cart_token']) || empty($_GET['cart_token'])) {
    http_response_code(400);
    $auth->printResponse([
        'status' => 'error',
        'message' => 'Cart Token is required',
    ]);
}

if (!isset($input['food_id'], $input['quantity'])) {
    http_response_code(400);
    $auth->printResponse([
        'status' => 'error',
        'message' => 'Food ID and Quantity are required',
    ]);
    exit;
}


$cart = new Cart();
$data = $cart->getCartByToken($_GET['cart_token']);

if (!$data) {
    http_response_code(404);
    $auth->printResponse([
        'status' => false,
        'message' => 'Cart not found'
    ]);
    exit;
}

$cart = new CartItem();
$result = $cart->updateItem($data['id'], $input['food_id'], $input['quantity']);

if (!$result) {
    http_response_code(404);
    $auth->printResponse([
        'status' => 'false',
        'message' => 'Cart item not found or item not updated'
    ]);
} else {
    http_response_code(200);
    $auth->printResponse([
        'status' => true,
        'message' => 'Cart item updated successfully'
    ]);
}
