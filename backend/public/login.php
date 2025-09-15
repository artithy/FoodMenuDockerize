<?php
require_once __DIR__ . '/cors.php';

require_once __DIR__ . '/../vendor/autoload.php';

use App\classes\Auth;

$auth = new Auth();
$auth->create_table();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    $auth->printResponse([
        'status' => false,
        'message' => 'Only POST method are allowed'
    ]);
}

$input = json_decode(file_get_contents('php://input'), true);
$data = $input ?? $_POST;

if (
    empty($data['email']) ||
    empty($data['password'])
) {
    http_response_code(400);
    $auth->printResponse([
        'status' => false,
        'message' => 'Both email and password required'
    ]);
}

$response = $auth->login($data['email'], $data['password']);
$auth->printResponse($response);
