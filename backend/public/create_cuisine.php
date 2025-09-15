<?php
require_once __DIR__ . '/cors.php';

require_once __DIR__ . '/../vendor/autoload.php';

use App\classes\Cuisine;

$cuisine = new Cuisine();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    $cuisine->printResponse([
        'status' => false,
        'message' => 'Only POST method is allowed'
    ]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$name = $input['name'] ?? null;

if ($name) {
    $created = $cuisine->create($name);
    if ($created) {
        echo json_encode(['status' => true, 'message' => 'Cuisine created successfully']);
    } else {
        echo json_encode(['status' => false, 'message' => 'Cuisine creation failed']);
    }
} else {
    echo json_encode(['status' => false, 'message' => 'Name is required']);
}
