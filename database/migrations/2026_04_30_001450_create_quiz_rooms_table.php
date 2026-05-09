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
        Schema::create('quiz_rooms', function (Blueprint $table) {
            $table->increments('id'); // Standard Integer to match your style
        
        // Match quizzes.id (Unsigned Integer)
        $table->unsignedInteger('quiz_id');
        $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');

        $table->string('room_code')->unique();
        $table->enum('status', ['waiting', 'active', 'finished'])->default('waiting');
        $table->integer('current_question_index')->default(0);
        $table->timestamp('question_expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_rooms');
    }
};
