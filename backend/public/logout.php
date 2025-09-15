<?php
require_once __DIR__ . '/cors.php';

require_once __DIR__ . '/../vendor/autoload.php';

use App\classes\Auth;

$auth = new Auth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    $auth->printResponse([
        'status' => false,
        'message' => 'Only POST method is allowed'
    ]);
    exit;
}


$token = $_SERVER["HTTP_AUTHORIZATION"] ?? null;
$parts = explode(' ', $token);
$token = $parts[1] ?? $parts[0];

if (!$token) {
    $auth->printResponse([
        'status' => false,
        'message' => 'Token missing'
    ]);
    exit;
}

$response = $auth->logout($token);
$auth->printResponse($response);
