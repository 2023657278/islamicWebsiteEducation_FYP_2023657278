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
    Schema::table('quizzes', function (Blueprint $table) {
        // Add Subtopic/Chapter name
        $table->string('topic')->nullable()->after('description'); 
        
        // Add Difficulty Level
        $table->enum('difficulty', ['Easy', 'Medium', 'Hard'])->default('Easy')->after('topic');
    });
}

public function down()
{
    Schema::table('quizzes', function (Blueprint $table) {
        $table->dropColumn(['topic', 'difficulty']);
    });
}
};
