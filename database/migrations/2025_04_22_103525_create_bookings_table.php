<?php

// Create Bookings Table Migration
// database/migrations/2024_04_22_000004_create_bookings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id('bookings_id');
            $table->foreignId('users_id')->constrained('users', 'users_id')->onDelete('cascade');
            $table->foreignId('vehicles_id')->constrained('vehicles', 'vehicles_id');
            $table->date('pickup_date');
            $table->date('return_date');
            $table->integer('total_price')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'canceled'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
}
