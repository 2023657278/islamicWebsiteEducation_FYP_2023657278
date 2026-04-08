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
    Schema::table('timetables', function (Blueprint $table) {
        // 1. Drop the foreign key constraint first
        $table->dropForeign(['user_id']); 
        
        // 2. Now drop the column
        $table->dropColumn('user_id');
    });
}

public function down(): void
{
    Schema::table('timetables', function (Blueprint $table) {
        $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
    });
}
};
