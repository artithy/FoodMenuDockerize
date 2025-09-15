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

$cuisine = new Cuisine();

$input = json_decode(file_get_contents('php://input'), true);
$name = $input['name'] ?? null;
$id = $input['id'] ?? null;

if ($name && $id) {
    $updated = $cuisine->update($id, $name);
    if ($updated) {
        echo json_encode(['status' => true, 'message' => 'Cuisine updated']);
    } else {
        echo json_encode(['status' => false, 'message' => 'Cuisine updated failed']);
    }
} else {
    echo json_encode(['status' => false, 'message' => 'Name and Id required']);
}
