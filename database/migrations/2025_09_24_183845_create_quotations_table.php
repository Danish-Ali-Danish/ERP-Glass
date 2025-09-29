<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->string('quote_no')->unique();
            $table->date('date');
            $table->string('to_name')->nullable();
            $table->string('company_name')->nullable();
            $table->string('project_name')->nullable();
            $table->string('location')->nullable();
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('vat', 12, 2)->default(0);
            $table->decimal('grand_total', 12, 2)->default(0);
            $table->text('terms')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
