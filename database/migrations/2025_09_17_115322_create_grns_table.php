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
    Schema::create('grns', function (Blueprint $table) {
        $table->id();
        $table->string('grn_no')->unique(); // Auto generated GRN number
        $table->unsignedBigInteger('lpo_id')->nullable(); // Foreign key to LPO

        $table->string('supplier_name')->nullable();
        $table->string('supplier_code')->nullable();

        $table->string('requested_by')->nullable();
        $table->unsignedBigInteger('department_id')->nullable();

        $table->string('inv_no')->nullable(); // Supplier Invoice/DN No
        $table->date('inv_date')->nullable(); // Supplier Invoice/DN Date

        $table->string('project_name')->nullable();

        $table->timestamps();

        // Relations
        $table->foreign('lpo_id')->references('id')->on('lpos')->onDelete('set null');
        $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grns');
    }
};
