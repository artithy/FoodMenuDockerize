<?php
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/../vendor/autoload.php';

use App\classes\Cuisine;

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    $cuisine->printResponse([
        'status' => false,
        'message' => 'Only GET method is allowed'
    ]);
    exit;
}

$cuisine = new Cuisine;

$input = json_decode(file_get_contents('php://input'), true);
$id = $input['id'] ?? null;

if ($id) {
    $name = $cuisine->getCuisineNameById($id);
    if ($name) {
        echo json_encode(['status' => true, 'name' => $name]);
    } else {
        echo json_encode(['status' => false, 'name' => 'Cuisine not found']);
    }
} else {
    echo json_encode(['status' => false, 'name' => 'Id required']);
}
