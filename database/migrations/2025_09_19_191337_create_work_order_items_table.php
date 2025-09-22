<?php

// database/migrations/xxxx_xx_xx_create_work_order_items_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained()->onDelete('cascade');
            $table->decimal('outer_w', 10, 2)->nullable();
            $table->decimal('outer_h', 10, 2)->nullable();
            $table->decimal('inner_w', 10, 2)->nullable();
            $table->decimal('inner_h', 10, 2)->nullable();
            $table->integer('qty')->default(1);
            $table->decimal('sqm', 10, 2)->nullable();
            $table->decimal('lm', 10, 2)->nullable();
            $table->decimal('chargeable_sqm', 10, 2)->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->text('instructions')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_order_items');
    }
};
