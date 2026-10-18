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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')
                ->constrained('bookings')
                ->onDelete('cascade');
            $table->string('transaction_reference', 64)->unique();
            $table->string('provider', 64);
            $table->string('provider_transaction_id', 128)->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('BDT');
            $table->string('payment_status', 32)->default('initiated');
            $table->string('payment_method', 64)->nullable();
            $table->json('gateway_payload')->nullable();
            $table->timestamp('initiated_at')->useCurrent();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->index(['provider', 'provider_transaction_id'], 'payments_provider_tran_idx');
            $table->index(['booking_id', 'payment_status'], 'payments_booking_status_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
