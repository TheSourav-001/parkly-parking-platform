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
        Schema::create('facility_vehicle_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parking_facility_id')
                ->constrained('parking_facilities')
                ->onDelete('cascade');
            $table->foreignId('vehicle_type_id')
                ->constrained('vehicle_types')
                ->onDelete('restrict');
            $table->decimal('rate_amount', 10, 2);
            $table->string('rate_unit', 32)->default('hour');
            $table->decimal('min_charge_amount', 10, 2)->default(0.00);
            $table->string('currency', 3)->default('BDT');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(
                ['parking_facility_id', 'vehicle_type_id', 'rate_unit'],
                'facility_vehicle_rate_unique'
            );
            $table->index(
                ['parking_facility_id', 'vehicle_type_id', 'is_active'],
                'facility_rates_search_idx'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facility_vehicle_rates');
    }
};
