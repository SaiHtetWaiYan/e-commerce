<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('store_name');
            $table->string('store_slug')->unique();
            $table->text('store_description')->nullable();
            $table->string('store_logo')->nullable();
            $table->string('store_banner')->nullable();
            $table->string('business_registration_no', 100)->nullable();
            $table->string('tax_id', 100)->nullable();
            $table->decimal('commission_rate', 5, 2)->default(10.00);
            $table->boolean('is_verified')->default(false);
            $table->string('bank_name', 100)->nullable();
            $table->string('bank_account_number', 50)->nullable();
            $table->string('bank_account_name')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index('store_slug');
            $table->index('is_verified');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_profiles');
    }
};
