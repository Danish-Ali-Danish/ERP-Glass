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
    Schema::create('grn_items', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('grn_id'); // FK to GRNs

        $table->string('item_code')->nullable();
        $table->string('description');
        $table->string('uom'); // Unit of Measure
        $table->decimal('quantity', 10, 2)->default(0); // Quantity received

        $table->timestamps();

        $table->foreign('grn_id')->references('id')->on('grns')->onDelete('cascade');
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
