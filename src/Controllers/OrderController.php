<?php

namespace App\Controllers;

use App\Services\OrderService;
use App\Utils\Auth;

class OrderController {
    private $orderService;

    public function __construct() {
        $this->orderService = new OrderService();
    }

    public function createOrder() {
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input['items']) || !is_array($input['items'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid items']);
            exit;
        }
        $order = $this->orderService->createOrder($input['items']);
        echo json_encode($order);
    }

    public function addItems($orderId) {
        $items = json_decode(file_get_contents('php://input'), true);
        if (!is_array($items)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid items']);
            exit;
        }
        $order = $this->orderService->addItems($orderId, $items);
        echo json_encode($order);
    }

    public function markDone($orderId) {
        Auth::authorize();
        $order = $this->orderService->markDone($orderId);
        echo json_encode($order);
    }

    public function listOrders() {
        Auth::authorize();
        $done = isset($_GET['done']) ? $_GET['done'] === '1' : null;
        $orders = $this->orderService->listOrders($done);
        echo json_encode($orders);
    }

    public function getOrder($orderId) {
        $order = $this->orderService->getOrder($orderId);
        if (!$order) {
            http_response_code(404);
            echo json_encode(['error' => 'Order not found']);
            exit;
        }
        echo json_encode($order);
    }
}