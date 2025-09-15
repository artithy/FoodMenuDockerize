<?php

use App\classes\Order;

require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/../vendor/autoload.php';

$appKey    = "";
$secretKey = "";


$orderId = $_GET['order_id'] ?? null;
$invoiceId = $_GET['invoice'] ?? null;

if (!$orderId || !$invoiceId) {
    echo json_encode([
        "status" => false,
        "message" => "Invoice Id or Order Id not found"
    ]);
}

$bearerToken = "Bearer " . base64_encode($appKey . ":" . md5($secretKey . time()));

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api-sandbox.portpos.com/payment/v2/invoice/$invoiceId");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: $bearerToken",
    "Content-Type: application/json"
]);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

$paymentStatus = $data['data']['order']['status'] ?? null;

$order = new Order();
$orderData = $order->pdo->query("SELECT * FROM orders WHERE order_id = " . $order->pdo->quote($orderId))->fetch();

if ($paymentStatus === 'ACCEPTED' && $orderData) {

    $order->update([
        "payment_status" => "paid",
        "invoice_id" => $invoiceId,
        "status" => "ordered"
    ], $orderData['id']);
    echo json_encode([
        "status" => true,
        "order_id" => $orderId,
        "invoice_id" => $invoiceId,
        "payment_status" => "SUCCESS",
        "message" => "Payment Successful"
    ]);
} else {
    echo json_encode([
        "status" => false,
        "order_id" => $orderId,
        "invoice_id" => $invoiceId,
        "payment_status" => "FAILED",
        "message" => "Payment Failed",
        "api_response" => $data
    ]);
}
