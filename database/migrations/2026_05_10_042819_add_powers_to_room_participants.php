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
    Schema::table('room_participants', function (Blueprint $table) {
        // 🟢 Only add MP if it doesn't exist yet
        if (!Schema::hasColumn('room_participants', 'mp')) {
            $table->integer('mp')->default(0);
        }

        // Add the other power-up columns
        $table->boolean('is_shielded')->default(false);
        $table->boolean('is_frozen')->default(false);
        $table->boolean('active_boost')->default(false);
    });
}

public function down(): void
{
    Schema::table('room_participants', function (Blueprint $table) {
        $table->dropColumn(['mp', 'is_shielded', 'is_frozen', 'active_boost']);
    });
}
};
