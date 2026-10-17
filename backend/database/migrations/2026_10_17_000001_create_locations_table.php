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
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('locations')
                ->onDelete('restrict');
            $table->string('name', 128);
            $table->string('type', 32); // division, district, thana, area
            $table->string('slug', 128);
            $table->timestamps();

            $table->unique(['parent_id', 'slug'], 'locations_parent_slug_unique');
            $table->index(['parent_id', 'type'], 'locations_parent_id_type_idx');
            $table->index('slug', 'locations_slug_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
