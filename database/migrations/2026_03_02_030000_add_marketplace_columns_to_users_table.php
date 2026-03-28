<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('phone', 20)->nullable()->after('email');
            $table->string('avatar')->nullable()->after('password');
            $table->string('role', 20)->default('customer')->after('avatar');
            $table->string('status', 20)->default('active')->after('role');
            $table->softDeletes()->after('remember_token');

            $table->index(['role', 'status']);
            $table->index('phone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropIndex('users_role_status_index');
            $table->dropIndex('users_phone_index');
            $table->dropSoftDeletes();
            $table->dropColumn(['phone', 'avatar', 'role', 'status']);
        });
    }
};
