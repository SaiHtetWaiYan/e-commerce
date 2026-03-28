<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table): void {
            $table->time('estimated_delivery_time_from')->nullable()->after('estimated_delivery_date');
            $table->time('estimated_delivery_time_to')->nullable()->after('estimated_delivery_time_from');
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table): void {
            $table->dropColumn(['estimated_delivery_time_from', 'estimated_delivery_time_to']);
        });
    }
};
