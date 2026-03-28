<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_payout_histories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('vendor_payout_id')->constrained()->cascadeOnDelete();
            $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 30);
            $table->text('note')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['vendor_payout_id', 'action']);
        });

        Schema::table('vendor_payouts', function (Blueprint $table): void {
            $table->unique(['vendor_id', 'period_start', 'period_end']);
        });
    }

    public function down(): void
    {
        Schema::table('vendor_payouts', function (Blueprint $table): void {
            $table->dropUnique('vendor_payouts_vendor_id_period_start_period_end_unique');
        });

        Schema::dropIfExists('vendor_payout_histories');
    }
};
