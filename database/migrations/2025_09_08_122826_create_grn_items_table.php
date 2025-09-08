<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
    Schema::create('grn_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('grn_id')->constrained()->onDelete('cascade');
        $table->string('item_code')->nullable();
        $table->string('description');
        $table->string('uom')->nullable();
        $table->integer('quantity');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grn_items');
    }
};
