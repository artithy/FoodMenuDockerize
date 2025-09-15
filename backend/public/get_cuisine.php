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

$cuisine = new Cuisine();
$data = $cuisine->getAll();

echo json_encode(['status' => true, 'data' => $data]);
