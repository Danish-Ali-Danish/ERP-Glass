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
        Schema::create('requisitions', function (Blueprint $table) {
            $table->id();
            $table->string('req_no')->unique();
            $table->date('date');
            $table->date('req_date');
            $table->string('requested_by');
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->string('project_name');
            $table->text('remarks')->nullable();
                $table->date('delivery_date')->nullable(); // <-- add this column

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requisitions');
    }
};
