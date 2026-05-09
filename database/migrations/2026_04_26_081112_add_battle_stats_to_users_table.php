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
        Schema::table('users', function (Blueprint $table) {
        // Battle Stats for the Islamic Warrior Arena
        $table->integer('hp')->default(100);          // Max Health
        $table->integer('xp')->default(0);            // Experience for leveling
        $table->integer('level')->default(1);         // Student Level
        $table->integer('rank_points')->default(0);   // Points for PvP Leaderboard
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
        $table->dropColumn(['hp', 'xp', 'level', 'rank_points']);
    });
    }
};
