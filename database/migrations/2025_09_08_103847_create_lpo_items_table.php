<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('lpo_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lpo_id')->constrained()->onDelete('cascade');
            $table->string('description');
            $table->decimal('area', 12,2)->nullable();
            $table->integer('quantity');
            $table->string('uom');
            $table->decimal('unit_price', 12,2);
            $table->decimal('total', 12,2);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('lpo_items');
    }
};
