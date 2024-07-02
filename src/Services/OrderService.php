<?php

namespace App\Services;

use App\Utils\IdGenerator;

class OrderService
{
    private $dataFile;
    private $idGenerator = null;
    private $storeBy;

    public function __construct()
    {
        $this->dataFile = __DIR__ . '/../../storage/orders.json';
        if (!file_exists($this->dataFile)) {
            file_put_contents($this->dataFile, json_encode([]));
        }

        $this->storeBy = (include __DIR__ . "/../../config.php")['store_by'];
        $this->idGenerator = new IdGenerator();
    }

    private function getOrders()
    {
        return json_decode(file_get_contents($this->dataFile), true);
    }

    private function saveOrders($orders)
    {
        file_put_contents($this->dataFile, json_encode($orders));
    }

    public function createOrder($items)
    {
        $this->checkItems($items);
        $orderId = $this->idGenerator->getNextId();
        if ($this->storeBy == "database") {
            $order = \App\Models\Database\Order::create([
                "id" => $orderId,
                "items" => $items,
                "done" => false
            ]);
            return $order;
        }
        $order = new \App\Models\File\Order($orderId, $items);
        $orders = $this->getOrders();
        $orders[] = $order->toArray();
        $this->saveOrders($orders);
        return $order->toArray();
    }

    private function checkItems($items) {
        foreach ($items as $item) {
            if ($item < 1 || $item > 5000) {
                http_response_code(400);
                echo json_encode(['error' => 'Invalid items']);
                exit;
            }
        }
    }

    public function addItems($orderId, $items)
    {
        $this->checkItems($items);
        if ($this->storeBy == "database") {
            $order = \App\Models\Database\Order::where('id', $orderId)->where('done', false)->first();
            if (!$order) {
                http_response_code(404);
                echo json_encode(['error' => 'Order not found or already done']);
                exit;
            }
            $order->items = array_merge($order->items, $items);
            $order->save();
            return $order->toArray();
        }
        $orders = $this->getOrders();
        foreach ($orders as &$order) {
            if ($order['order_id'] === $orderId && !$order['done']) {
                $order['items'] = array_merge($order['items'], $items);
                $this->saveOrders($orders);
                return $order;
            }
        }
        http_response_code(404);
        echo json_encode(['error' => 'Order not found or already done']);
        exit;
    }

    public function markDone($orderId)
    {
        if ($this->storeBy == "database") {
            $order = \App\Models\Database\Order::where('id', $orderId)->where('done', false)->first();
            if (!$order) {
                http_response_code(404);
                echo json_encode(['error' => 'Order not found or already done']);
                exit;
            }
            $order->done = true;
            $order->save();
            return $order->toArray();
        }
        $orders = $this->getOrders();
        foreach ($orders as &$order) {
            if ($order['order_id'] === $orderId && !$order['done']) {
                $order['done'] = true;
                $this->saveOrders($orders);
                return $order;
            }
        }
        http_response_code(404);
        echo json_encode(['error' => 'Order not found or already done']);
        exit;
    }

    public function listOrders($done)
    {
        if ($this->storeBy == "database") {
            if ($done === null) {
                $models = \App\Models\Database\Order::all();
            } else {
                $models = \App\Models\Database\Order::where('done', $done)->get();
            }
            $orders = [];
            foreach ($models as $model) {
                $orders[] = $model->toArray();
            }
        } else {
            $orders = $this->getOrders();
            if ($done !== null) {
                $orders = array_filter($orders, function ($order) use ($done) {
                    return $order['done'] === $done;
                });
            }
        }
        return array_values($orders);
    }

    public function getOrder($orderId)
    {
        if ($this->storeBy == "database") {
            $model = \App\Models\Database\Order::find($orderId);
            if ($model) {
                return $model->toArray();
            }
            http_response_code(404);
            echo json_encode(['error' => 'Order not found']);
        }
        $orders = $this->getOrders();
        foreach ($orders as $order) {
            if ($order['order_id'] === $orderId) {
                return $order;
            }
        }
        http_response_code(404);
        echo json_encode(['error' => 'Order not found']);
    }
}