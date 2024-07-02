<?php

namespace App\Models\File;

class Order {
    public $order_id;
    public $items;
    public $done;

    public function __construct($order_id, $items) {
        $this->order_id = $order_id;
        $this->items = $items;
        $this->done = false;
    }

    public function toArray() {
        return [
            'order_id' => $this->order_id,
            'items' => $this->items,
            'done' => $this->done
        ];
    }
}