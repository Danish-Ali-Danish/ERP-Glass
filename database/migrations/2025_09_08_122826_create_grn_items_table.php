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
   Schema::create('grn_items', function(Blueprint $table){
    $table->id();
    $table->unsignedBigInteger('grn_id');
    $table->string('description')->nullable();
    $table->string('uom')->nullable();
    $table->decimal('quantity',12,2)->default(0);
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
