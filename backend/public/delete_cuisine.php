<?php
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/../vendor/autoload.php';

use App\classes\Cuisine;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    $cuisine->printResponse([
        'status' => false,
        'message' => 'Only POST method is allowed'
    ]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$id = $input['id'] ?? null;

$cuisine = new Cuisine();

if ($id) {
    $deleted = $cuisine->delete($id);
    if ($deleted) {
        echo json_encode(['status' => true, 'message' => 'Cuisine Delete Successfully']);
    } else {
        echo json_encode(['status' => false, 'message' => 'Failed to Delete cuisine']);
    }
} else {
    echo json_encode(['status' => false, 'message' => 'Id required']);
}
