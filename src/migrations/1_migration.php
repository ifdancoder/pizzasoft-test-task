<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration {
    public function up(): void {
        Capsule::schema()->create('orders', function (Blueprint $table) {
            $table->string('id', 15)->primary();
            $table->json('items');
            $table->boolean('done')->default(false);
        });
    }

    public function down(): void {
        Capsule::schema()->dropIfExists('orders');
    }
};