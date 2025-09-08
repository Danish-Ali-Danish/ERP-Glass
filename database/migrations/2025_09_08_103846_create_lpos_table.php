<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('lpos', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_name');
            $table->date('date');
            $table->string('contact_person')->nullable();
            $table->string('lpo_no')->unique();
            $table->string('contact_no')->nullable();
            $table->string('pi_no')->nullable();
            $table->string('supplier_trn')->nullable();
            $table->text('address')->nullable();
            $table->decimal('sub_total', 12,2)->default(0);
            $table->decimal('vat', 12,2)->default(0);
            $table->decimal('net_total', 12,2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('lpos');
    }
};
