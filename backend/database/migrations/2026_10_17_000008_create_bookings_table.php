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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_reference', 32)->unique();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->foreignId('parking_facility_id')
                ->constrained('parking_facilities')
                ->onDelete('restrict');
            $table->foreignId('vehicle_type_id')
                ->constrained('vehicle_types')
                ->onDelete('restrict');
            $table->string('vehicle_plate_number', 32)->nullable();
            $table->string('customer_name', 128);
            $table->string('customer_phone', 32);
            $table->string('customer_email', 128)->nullable();
            $table->dateTime('planned_start_at');
            $table->unsignedInteger('duration_minutes');
            $table->dateTime('planned_end_at');
            $table->decimal('unit_rate', 10, 2);
            $table->string('rate_unit', 32)->default('hour');
            $table->decimal('base_amount', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->string('currency', 3)->default('BDT');
            $table->string('booking_status', 32)->default('pending_payment');
            $table->string('payment_status', 32)->default('initiated');
            $table->boolean('capacity_held')->default(false);
            $table->timestamp('hold_expires_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at'], 'bookings_driver_lookup_idx');
            $table->index(
                ['parking_facility_id', 'booking_status', 'planned_start_at'],
                'bookings_facility_status_idx'
            );
            $table->index(
                ['booking_status', 'capacity_held', 'hold_expires_at'],
                'bookings_hold_expiry_idx'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
