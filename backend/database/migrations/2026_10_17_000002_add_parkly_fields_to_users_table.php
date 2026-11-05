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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 32)->nullable()->after('email');
            $table->string('role', 32)->default('driver')->after('password');
            $table->boolean('is_active')->default(true)->after('role');

            $table->index('role', 'users_role_idx');
            $table->index('is_active', 'users_is_active_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_role_idx');
            $table->dropIndex('users_is_active_idx');
            $table->dropColumn(['phone', 'role', 'is_active']);
        });
    }
};
