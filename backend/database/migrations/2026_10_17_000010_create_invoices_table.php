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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')
                ->unique()
                ->constrained('bookings')
                ->onDelete('restrict');
            $table->string('invoice_number', 64)->unique();
            $table->decimal('amount_paid', 10, 2);
            $table->string('currency', 3)->default('BDT');
            $table->string('billing_name', 128);
            $table->string('billing_phone', 32);
            $table->string('pdf_path', 255)->nullable();
            $table->timestamp('issued_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
