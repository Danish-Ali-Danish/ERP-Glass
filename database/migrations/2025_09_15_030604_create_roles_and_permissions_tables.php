<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Roles
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g. admin, editor
            $table->string('display_name')->nullable(); // friendly name
            $table->string('guard_name')->default('web');
            $table->timestamps();
        });

        // Permissions
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // e.g. create_users
            $table->string('display_name')->nullable();
            $table->string('group')->nullable(); // module grouping e.g. Users, Products
            $table->string('guard_name')->default('web');
            $table->timestamps();
        });

        // role_user pivot (assign roles to users)
        Schema::create('role_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['role_id','user_id']);
        });

        // permission_role pivot (link permissions to roles)
        Schema::create('permission_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('roles')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['permission_id','role_id']);
        });

        // optional: permission_user pivot for direct user permissions (advanced)
        Schema::create('permission_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('permission_id')->constrained('permissions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['permission_id','user_id']);
        });
    }

    public function down(): void
    {
        // drop in reverse order to avoid FK errors
        Schema::dropIfExists('permission_user');
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
