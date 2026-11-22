<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('topic')->default('general');
            $table->string('facility_reference')->nullable();
            $table->text('message');
            $table->string('ip_address', 45)->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });

        Schema::create('operator_enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('facility_name');
            $table->string('contact_name');
            $table->string('email');
            $table->string('phone');
            $table->string('location_zone');
            $table->string('address');
            $table->integer('capacity')->unsigned()->default(10);
            $table->text('notes')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('status')->default('received');
            $table->timestamps();
        });

        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('ip_address', 45)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_subscribers');
        Schema::dropIfExists('operator_enquiries');
        Schema::dropIfExists('contact_messages');
    }
};
