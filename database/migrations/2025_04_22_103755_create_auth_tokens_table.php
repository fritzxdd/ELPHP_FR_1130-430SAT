<?php

// Create Auth Tokens Table Migration
// database/migrations/2024_04_22_000006_create_auth_tokens_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateAuthTokensTable extends Migration
{
    public function up(): void
    {
        Schema::create('auth_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users', 'users_id')->onDelete('cascade');
            $table->string('token', 255);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('expires_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            // OR use this alternative if you want tokens to expire 24 hours after creation:
            // $table->timestamp('expires_at')->default(DB::raw('DATE_ADD(CURRENT_TIMESTAMP, INTERVAL 24 HOUR)'));
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auth_tokens');
    }
}