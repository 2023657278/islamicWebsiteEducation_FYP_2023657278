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
    // 🟢 Only add the column if it DOES NOT exist yet
    if (!Schema::hasColumn('quiz_rooms', 'is_public')) {
        Schema::table('quiz_rooms', function (Blueprint $table) {
            $table->boolean('is_public')->default(true)->after('room_code');
        });
    }
}

public function down(): void
{
    if (Schema::hasColumn('quiz_rooms', 'is_public')) {
        Schema::table('quiz_rooms', function (Blueprint $table) {
            $table->dropColumn('is_public');
        });
    }
}
};
