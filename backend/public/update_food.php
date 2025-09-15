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

$base64Image = $input['image'];
$imageInfo = explode(',', $base64Image);
$extension = str_replace(["data:image/", ";base64"], "", $imageInfo[0]);
$imageName = "images/" . uniqid() . "." . $extension;

file_put_contents(__DIR__ . '/' . $imageName, base64_decode($imageInfo[1]));
$input['image'] = $imageName;


$food = new Food();
if ($input) {
    $updated = $food->update($input['id'], $input);
    if ($updated) {
        echo json_encode(['status' => true, 'message' => 'Food updated successfully']);
    } else {
        echo json_encode(['status' => false, 'message' => 'Food update failed']);
    }
} else {
    echo json_encode(['status' => false, 'message' => 'Invalid input']);
}
