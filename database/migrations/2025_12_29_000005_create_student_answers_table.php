<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_create_student_answers_table.php

public function up()
{
    Schema::create('student_answers', function (Blueprint $table) {
        $table->increments('id');
        
        // FIX: Match results.id (Integer)
        $table->unsignedInteger('result_id');
        $table->foreign('result_id')->references('id')->on('results')->onDelete('cascade');
        
        // FIX: Match questions.id (Integer)
        $table->unsignedInteger('question_id');
        $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
        
        // FIX: Match options.id (Integer)
        $table->unsignedInteger('option_id');
        $table->foreign('option_id')->references('id')->on('options')->onDelete('cascade');
        
        $table->boolean('is_correct')->default(false);
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_answers');
    }
};
