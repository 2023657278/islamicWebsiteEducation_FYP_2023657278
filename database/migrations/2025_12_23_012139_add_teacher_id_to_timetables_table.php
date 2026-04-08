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
        // Use unsignedBigInteger to perfectly match the 'id' in the users table
        $table->unsignedInteger('teacher_id')->nullable()->after('id');
        
        // Add the foreign key constraint separately to be safe
        $table->foreign('teacher_id')
              ->references('id')
              ->on('users')
              ->onDelete('cascade');
    });
}

public function down(): void
{
    Schema::table('timetables', function (Blueprint $table) {
        Schema::dropIfExists('timetables');
    });
}
};
