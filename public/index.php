<?php

require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../src/bootstrap.php';

use App\Controllers\OrderController;

$method = $_SERVER['REQUEST_METHOD'];
$path = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

$last = strstr(end($path), '?', true) ?: end($path);

$path[key($path)] = $last;

$controller = new OrderController();


switch ($method) {
    case 'POST':
        if ($path[0] === 'orders') {
            if (count($path) == 1) {
                $controller->createOrder();
            } elseif (count($path) == 3 && $path[2] === 'items') {
                $controller->addItems($path[1]);
            } elseif (count($path) == 3 && $path[2] === 'done') {
                $controller->markDone($path[1]);
            }
        }
        break;
    case 'GET':
        if ($path[0] === 'orders') {
            if (count($path) == 1) {
                $controller->listOrders();
            } elseif (count($path) == 2) {
                $controller->getOrder($path[1]);
            }
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
        break;
}