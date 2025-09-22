<?php

// database/migrations/xxxx_xx_xx_create_work_orders_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->string('work_order_no')->unique();   // EPGI-0001
            $table->string('customer_name');
            $table->string('customer_mobile')->nullable();
            $table->date('date');
            $table->string('work_order_type')->nullable();
            $table->string('customer_ref')->nullable();
            $table->json('processes')->nullable();       // store checkboxes
            $table->decimal('extra_price_sqm', 10, 2)->nullable();
            $table->decimal('extra_total', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_orders');
    }
};
