<?php
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/../vendor/autoload.php';

use App\classes\Food;

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    $food->printResponse([
        'status' => false,
        'message' => 'Only GET method is allowed'
    ]);
    exit;
}

$food = new Food();
$data = $food->getAll();

echo json_encode(['status' => true, 'data' => $data]);
