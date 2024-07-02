<?php

namespace App\Models\Database;

use Illuminate\Database\Eloquent\Model;

class Order extends Model {
    protected $table = 'orders';
    protected $primaryKey = 'id';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id',
        'items',
        'done'
    ];

    protected $casts = [
        'items' => 'array',
        'done' => 'boolean',
    ];
}