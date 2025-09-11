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
    {Schema::create('grns', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('lpo_id')->nullable();
        $table->string('lpo_no')->nullable();
        $table->string('supplier_name')->nullable();
        $table->date('date')->nullable();
        $table->string('supplier_code')->nullable();
        $table->string('requested_by')->nullable();
        $table->string('inv_no')->nullable();
        $table->string('department')->nullable();
        $table->date('inv_date')->nullable();
        $table->string('project_name')->nullable();
        $table->timestamps();
    });}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grns');
    }
};
