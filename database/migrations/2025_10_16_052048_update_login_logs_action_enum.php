<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update the enum values for action column
        DB::statement("ALTER TABLE login_logs MODIFY COLUMN action ENUM('login', 'register', 'logout', 'password_reset') DEFAULT 'login'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values
        DB::statement("ALTER TABLE login_logs MODIFY COLUMN action ENUM('login', 'register', 'logout') DEFAULT 'login'");
    }
};