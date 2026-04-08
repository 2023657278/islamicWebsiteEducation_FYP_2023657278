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
        Schema::create('timetables', function (Blueprint $table) {
            $table->increments ('id');
           // 1. DECLARE COLUMNS FIRST
        $table->unsignedInteger('group_id');
        $table->unsignedInteger('subject_id');
        $table->unsignedInteger('day_id');
        $table->unsignedInteger('year_id');
        $table->unsignedInteger('user_id'); // This was the missing one!
            $table->string ('time_from')->nullable();
            $table->string ('time_to')->nullable();
            $table->timestamps();

            // 2. THEN DEFINE FOREIGN KEYS
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
        $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
        $table->foreign('day_id')->references('id')->on('days')->onDelete('cascade');
        $table->foreign('year_id')->references('id')->on('years')->onDelete('cascade');
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timetables');
    }
};
