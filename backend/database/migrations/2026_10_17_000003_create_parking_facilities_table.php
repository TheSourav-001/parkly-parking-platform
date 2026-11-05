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
        Schema::create('parking_facilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')
                ->nullable()
                ->constrained('locations')
                ->onDelete('set null');
            $table->string('name', 255);
            $table->string('slug', 255)->unique();
            $table->text('address_text');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'latitude', 'longitude'], 'facilities_search_coords_idx');
            $table->index(['location_id', 'is_active'], 'facilities_location_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parking_facilities');
    }
};
