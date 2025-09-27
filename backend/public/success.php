<?php

use SendGrid\Mail\Mail;
use App\classes\Order;

require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/../vendor/autoload.php';

$appKey    = "";
$secretKey = "";
$sendgridapikey = "";

$orderId   = $_GET['order_id'] ?? null;
$invoiceId = $_GET['invoice'] ?? null;

if (!$orderId || !$invoiceId) {
    echo json_encode([
        "status" => false,
        "message" => "Invoice Id or Order Id not found"
    ]);
    exit();
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

    $email = new Mail();
    $email->setFrom("orders@tithy.com", "Tithy Shop");
    $email->setSubject("Order Confirmation #$orderId");
    $email->addTo($orderData['email'], $orderData['customer_name']);
    $email->addContent("text/plain", "Your payment has been accepted. Order ID: $orderId");
    $email->addContent("text/html", "<strong>Your payment has been accepted. Order ID: $orderId.</strong>");

    $sendgrid = new \SendGrid($sendgridapikey);
    try {
        $sendgrid->send($email);
    } catch (Exception $e) {
        error_log('SendGrid Error: ' . $e->getMessage());
    }

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
