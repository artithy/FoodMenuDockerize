<?php
require_once __DIR__ . '/cors.php';
require_once __DIR__ . '/../vendor/autoload.php';

use App\classes\Food;

$food = new Food();
$food->createTable();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status' => false,
        'message' => 'Only POST method is allowed'
    ]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

// Validate required fields
if (empty($input['name']) || empty($input['price']) || empty($input['cuisine_id'])) {
    http_response_code(400);
    echo json_encode([
        'status' => false,
        'message' => 'Name, price and cuisine_id are required'
    ]);
    exit;
}

// Handle base64 image if exists
if (!empty($input['image'])) {
    if (!is_dir(__DIR__ . '/images')) {
        mkdir(__DIR__ . '/images', 0777, true);
    }

    $base64Image = $input['image'];
    $imageInfo = explode(',', $base64Image);
    $extension = str_replace(["data:image/", ";base64"], "", $imageInfo[0]);
    $imageName = "images/" . uniqid() . "." . $extension;

    file_put_contents(__DIR__ . '/' . $imageName, base64_decode($imageInfo[1]));
    $input['image'] = $imageName;
} else {
    $input['image'] = null;
}

// Create food
$created = $food->create($input);

if ($created) {
    echo json_encode(['status' => true, 'message' => 'Food created successfully']);
} else {
    http_response_code(500);
    echo json_encode(['status' => false, 'message' => 'Food creation failed']);
}
