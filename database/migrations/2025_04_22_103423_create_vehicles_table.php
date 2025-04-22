<?php

// Create Vehicles Table Migration
// database/migrations/2024_04_22_000002_create_vehicles_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehiclesTable extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id('vehicles_id');
            $table->foreignId('users_id')->constrained('users', 'users_id')->onDelete('cascade');
            $table->string('vehicles_name');
            $table->string('plate_number');
            $table->string('model');
            $table->string('fuel_type');
            $table->string('price_per_day');
            $table->string('location');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
}
