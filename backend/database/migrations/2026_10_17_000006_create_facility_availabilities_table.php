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
        Schema::create('facility_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parking_facility_id')
                ->constrained('parking_facilities')
                ->onDelete('cascade');
            $table->foreignId('vehicle_type_id')
                ->constrained('vehicle_types')
                ->onDelete('restrict');
            $table->unsignedInteger('total_slots');
            $table->unsignedInteger('available_slots');
            $table->foreignId('last_updated_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->timestamp('last_updated_at');
            $table->timestamps();

            $table->unique(
                ['parking_facility_id', 'vehicle_type_id'],
                'facility_availability_unique'
            );
            $table->index(
                ['parking_facility_id', 'available_slots'],
                'facility_availabilities_lookup_idx'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facility_availabilities');
    }
};
