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
        Schema::create('srs_logs', function (Blueprint $table) {
            $table->increments('id');
        $table->unsignedInteger('user_id');
        $table->unsignedInteger('flashcard_id');
        
        // SRS Logic Columns
        $table->integer('box_number')->default(1); // For the Leitner System
        $table->float('ease_factor')->default(2.5); // SM-2 Algorithm standard
        $table->integer('interval')->default(0);
        $table->integer('repetition_count')->default(0);
        $table->date('next_review_date'); // When the card reappears
        
        $table->timestamps();

        // Foreign Keys
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        $table->foreign('flashcard_id')->references('id')->on('flashcards')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('srs_logs');
    }
};
