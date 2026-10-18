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
        Schema::create('availability_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parking_facility_id')
                ->constrained('parking_facilities')
                ->onDelete('cascade');
            $table->foreignId('vehicle_type_id')
                ->constrained('vehicle_types')
                ->onDelete('restrict');
            $table->unsignedInteger('old_available_slots');
            $table->unsignedInteger('new_available_slots');
            $table->string('change_reason', 255)->nullable();
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['parking_facility_id', 'created_at'], 'availability_history_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('availability_updates');
    }
};
