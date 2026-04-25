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
        Schema::create('flashcards', function (Blueprint $table) {
        $table->increments('id');
        $table->unsignedInteger('quiz_id')->nullable();
        $table->unsignedInteger('teacher_id');
        $table->unsignedInteger('subject_id'); // Add this missing column
        
        $table->text('question');
        $table->text('answer');
        $table->string('topic')->nullable(); // Change 'topics' to 'topic'
        
        $table->timestamps();

        // Foreign Keys
        $table->foreign('teacher_id')->references('id')->on('users')->onDelete('cascade');
        $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
        $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('flashcards');
    }
};
