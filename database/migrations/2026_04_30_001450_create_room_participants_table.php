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
        Schema::create('room_participants', function (Blueprint $table) {
            $table->increments('id');

        // Match quiz_rooms.id
        $table->unsignedInteger('room_id');
        $table->foreign('room_id')->references('id')->on('quiz_rooms')->onDelete('cascade');

        // Match users.id
        $table->unsignedInteger('user_id');
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

        $table->integer('hp')->default(100);
        $table->integer('mp')->default(20);
        $table->integer('last_rank')->nullable();
        $table->boolean('is_ready')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('room_participants');
    }
};
