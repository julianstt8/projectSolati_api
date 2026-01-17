<?php

header('Content-Type: application/json; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

$allowedOrigins = [
    'http://localhost:4200'
];

if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowedOrigins, true)) {
    header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
}

header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-API-KEY');


if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/../app/Database/Connection.php';

require_once __DIR__ . '/../app/Models/Task.php';

require_once __DIR__ . '/../app/DAOs/TaskDAO.php';
require_once __DIR__ . '/../app/DAOs/TaskDAOImpl.php';

require_once __DIR__ . '/../app/Services/TaskService.php';

require_once __DIR__ . '/../app/Controllers/TaskController.php';

require_once __DIR__ . '/../app/Routes/Api.php';

use App\Routes\Api;

Api::handleRequest();
