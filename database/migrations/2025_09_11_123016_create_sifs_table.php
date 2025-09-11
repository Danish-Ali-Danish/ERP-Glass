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
        Schema::create('sifs', function (Blueprint $table) {
            $table->id();
            $table->string('sif_no')->unique(); // SIF No
            $table->date('date')->nullable(); // Date
            $table->date('issued_date')->nullable(); // Issued Date
            $table->string('requested_by')->nullable();
            $table->string('department')->nullable();
            $table->string('project_name')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sifs');
    }
};
