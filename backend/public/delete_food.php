<?php
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/../vendor/autoload.php';

use App\classes\Food;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    $food->printResponse([
        'status' => false,
        'message' => 'Only POST method is allowed'
    ]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input['id']) {
    echo json_encode(['status' => false, 'message' => 'Id required']);
    return;
}

$food = new Food();
if ($input) {
    $updated = $food->delete($input['id']);
    if ($updated) {
        echo json_encode(['status' => true, 'message' => 'Food deleted successfully']);
    } else {
        echo json_encode(['status' => false, 'message' => 'Food delete failed']);
    }
} else {
    echo json_encode(['status' => false, 'message' => 'Invalid input']);
}
