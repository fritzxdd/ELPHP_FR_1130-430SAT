<?php

// Create Bookmarks Table Migration
// database/migrations/2024_04_22_000003_create_bookmarks_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookmarksTable extends Migration
{
    public function up(): void
    {
        Schema::create('bookmarks', function (Blueprint $table) {
            $table->id('bookmarks_id');
            $table->foreignId('users_id')->constrained('users', 'users_id')->onDelete('cascade');
            $table->foreignId('vehicles_id')->constrained('vehicles', 'vehicles_id')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookmarks');
    }
}
