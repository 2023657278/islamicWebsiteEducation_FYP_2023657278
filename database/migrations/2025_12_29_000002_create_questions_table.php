<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_create_questions_table.php

public function up()
{
    Schema::create('questions', function (Blueprint $table) {
        $table->increments('id');
        
        // FIX: Match quizzes.id (Integer)
        $table->unsignedInteger('quiz_id');
        $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');
        
        $table->text('question_text');
        $table->integer('points')->default(1);
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
