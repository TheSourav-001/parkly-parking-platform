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
        Schema::create('admin_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipient_user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('cascade');
            $table->foreignId('booking_id')
                ->nullable()
                ->constrained('bookings')
                ->onDelete('cascade');
            $table->string('type', 64);
            $table->string('title', 128);
            $table->text('message');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['recipient_user_id', 'read_at'], 'admin_notifications_user_unread_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_notifications');
    }
};
