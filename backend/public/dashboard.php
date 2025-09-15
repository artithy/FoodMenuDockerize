<?php
require_once __DIR__ . '/cors.php';

require_once __DIR__ . '/../vendor/autoload.php';

use App\classes\Auth;

$auth = new Auth();

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    $auth->printResponse([
        'status' => false,
        'message' => 'Only GET method is allowed'
    ]);
    exit;
}

$token = $_SERVER["HTTP_AUTHORIZATION"] ?? null;

if (!$token) {
    $auth->printResponse([
        'status' => false,
        'message' => 'Token required'
    ]);
    exit;
}


$parts = explode(' ', $token);
$token = $parts[1] ?? $parts[0];

$response = $auth->dashboard($token);
$auth->printResponse($response);
