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
    Schema::table('flashcards', function (Blueprint $table) {
        // CHANGED: Use 'unsignedInteger' instead of 'foreignId' to match the Quizzes table
        $table->unsignedInteger('quiz_id')->nullable()->after('id');
        
        // Manually add the foreign key constraint
        $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */

public function down()
{
    Schema::table('flashcards', function (Blueprint $table) {
        $table->dropForeign(['quiz_id']);
        $table->dropColumn('quiz_id');
    });
}
};
