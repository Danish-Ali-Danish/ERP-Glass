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
    Schema::table('lpos', function (Blueprint $table) {
        $table->boolean('grn_generated')->default(0)->after('id');
    });
}

public function down(): void
{
    Schema::table('lpos', function (Blueprint $table) {
        $table->dropColumn('grn_generated');
    });
}

};
