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
        Schema::table('locations', function (Blueprint $table) {
            $table->string('name_en', 128)->nullable()->after('name');
            $table->string('name_bn', 128)->nullable()->after('name_en');
            $table->decimal('latitude', 10, 7)->nullable()->after('slug');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->boolean('is_active')->default(true)->after('longitude');
        });

        Schema::table('parking_facilities', function (Blueprint $table) {
            $table->foreignId('area_id')->nullable()->after('location_id')->constrained('locations')->nullOnDelete();
            $table->foreignId('thana_id')->nullable()->after('area_id')->constrained('locations')->nullOnDelete();
            $table->foreignId('district_id')->nullable()->after('thana_id')->constrained('locations')->nullOnDelete();
            $table->foreignId('division_id')->nullable()->after('district_id')->constrained('locations')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parking_facilities', function (Blueprint $table) {
            $table->dropForeign(['area_id']);
            $table->dropForeign(['thana_id']);
            $table->dropForeign(['district_id']);
            $table->dropForeign(['division_id']);
            $table->dropColumn(['area_id', 'thana_id', 'district_id', 'division_id']);
        });

        Schema::table('locations', function (Blueprint $table) {
            $table->dropColumn(['name_en', 'name_bn', 'latitude', 'longitude', 'is_active']);
        });
    }
};

