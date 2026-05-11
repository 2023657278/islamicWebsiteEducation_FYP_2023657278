<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::dropIfExists('quiz_question');

    Schema::create('quiz_question', function (Blueprint $table) {
        $table->id();

        // 🟢 We use unsignedBigInteger because it's the Laravel default for IDs.
        // If your 'quizzes' table is very old, you might need 'unsignedInteger' instead.
        $table->unsignedInteger('quiz_id');
        $table->unsignedInteger('question_id');

        $table->foreign('quiz_id')
              ->references('id')
              ->on('quizzes')
              ->onDelete('cascade');

        $table->foreign('question_id')
              ->references('id')
              ->on('questions')
              ->onDelete('cascade');

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */

public function down()
{
    Schema::dropIfExists('quiz_question');
}
};
