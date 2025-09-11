<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sif_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sif_id')->constrained()->onDelete('cascade');
            $table->string('item_code');
            $table->string('description');
            $table->string('uom');
            $table->integer('quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sif_items');
    }
};
