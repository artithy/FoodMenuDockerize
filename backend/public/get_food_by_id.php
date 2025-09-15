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
$input = json_decode(file_get_contents('php://input'), true);

if (!$input['id']) {
    echo json_encode(['status' => false, 'message' => 'Id required']);
}

$food = new Food();
if ($input) {
    $getId = $food->getById($input['id']);
    if ($getId) {
        echo json_encode(['status' => true, 'message' => 'Food get successfully']);
    } else {
        echo json_encode(['status' => false, 'message' => 'Food not found']);
    }
} else {
    echo json_encode(['status' => true, 'message' => 'Invalid input']);
}
