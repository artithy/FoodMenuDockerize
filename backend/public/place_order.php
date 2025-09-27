<?php
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/../vendor/autoload.php';

use App\classes\Cart;
use App\classes\Order;
use App\classes\OrderItem;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => false,
        'message' => 'Only POST allowed'
    ]);
    exit();
}

$input = json_decode(file_get_contents('php://input'), true);

if (
    !isset($input['cart_token'], $input['customer_name'], $input['delivery_address'], $input['phone_number'], $input['items'], $input['email']) ||
    empty($input['items'])
) {
    echo json_encode([
        'status' => false,
        'message' => 'Required field missing'
    ]);
    exit();
}
$appKey    = "";
$secretKey = "";


$cart = new Cart();
$cartData = $cart->getCartByToken($input['cart_token']);
if (!$cartData) {
    echo json_encode([
        'status' => false,
        'message' => 'Cart not found'
    ]);
    exit();
}

$order = new Order();
$orderItem = new OrderItem();

$order->createTable();
$orderItem->createTable();
$orderStringId = 'ORD_' . bin2hex(random_bytes(8));
$orderId = $order->createOrder(
    $cartData['id'],
    $input['customer_name'],
    $input['delivery_address'],
    $input['phone_number'],
    $input['order_notes'] ?? '',
    $input['total_price'] ?? 0.00,
    $orderStringId
);

$orderItem->addItems($orderId, $input['items']);

$appKey    = "";
$secretKey = "";



$postUrl = "https://api-sandbox.portpos.com/payment/v2/invoice";
$bearerToken = "Bearer " . base64_encode($appKey . ":" . md5($secretKey . time()));

$productNames = [];
foreach ($input['items'] as $item) {
    $productNames[] = $item['food_name'] . " x" . $item['quantity'];
}
$productString = implode(", ", $productNames);

$data = [
    "order" => [
        "amount" => (float)($input['total_price'] ?? 0.00),
        "currency" => "BDT",
        "redirect_url" => "http://localhost:5173/success?order_id=$orderStringId&invoice=" . ($result['data']['invoice_id'] ?? '')

    ],
    "product" => [
        "name" => $productString,
        "description" => "Restaurant Order #$orderId"
    ],
    "billing" => [
        "customer" => [
            "name" => $input['customer_name'],
            "email" => $input['email'],
            "phone" => $input['phone_number'],
            "address" => [
                "street" => $input['delivery_address'],
                "city" => "Dhaka",
                "state" => "Dhaka",
                "zipcode" => 1207,
                "country" => "BD"
            ]
        ]
    ]
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $postUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: $bearerToken",
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);
$invoice_id = $result['data']['invoice_id'] ?? null;

if ($invoice_id) {
    $order->update(['invoice_id' => $invoice_id], $orderId);
}
if (isset($result['data']['action']['url'])) {
    $paymentUrl = $result['data']['action']['url'];

    echo json_encode([
        'status' => true,
        'order_id' => $orderStringId,
        'invoice_id' => $result['data']['invoice_id'] ?? null,
        'payment_url' => $paymentUrl,
        'message' => 'Order created and invoice generated successfully'
    ]);
} else {
    echo json_encode([
        'status' => false,
        'order_id' => $orderStringId,
        'message' => 'Invoice creation failed or Payment URL not found',
        'response' => $result
    ]);
}
