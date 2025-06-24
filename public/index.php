<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\ApiController;

$config = require __DIR__ . '/../config/database.php';
$db = new \PDO(
    "mysql:host={$config['host']};dbname={$config['database']}",
    $config['username'],
    $config['password'],
    [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
);

$controller = new ApiController($db);

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Handle input data
$data = [];
if ($method === 'POST' || $method === 'PUT') {
    if (!empty($_POST)) {
        $data = $_POST; // Form data
    } else {
        $input = file_get_contents('php://input');
        if (!empty($input) && is_string($input)) {
            $decoded = json_decode($input, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $data = $decoded;
            }
        }
    }
} elseif ($method === 'GET') {
    $data = $_GET;
}

header('Content-Type: application/json');
echo json_encode($controller->handleRequest($method, $path, $data));