<?php

// Create Reviews Table Migration
// database/migrations/2024_04_22_000005_create_reviews_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id('reviews_id');
            $table->foreignId('users_id')->constrained('users', 'users_id')->onDelete('cascade');
            $table->foreignId('vehicles_id')->constrained('vehicles', 'vehicles_id')->onDelete('cascade');
            $table->integer('rating');
            $table->text('comment')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
}

