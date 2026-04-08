<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_create_results_table.php

public function up()
{
    Schema::create('results', function (Blueprint $table) {
        $table->increments('id');
        
        // FIX: Match users.id (Integer)
        $table->unsignedInteger('user_id');
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

        // FIX: Match quizzes.id (Integer)
        $table->unsignedInteger('quiz_id');
        $table->foreign('quiz_id')->references('id')->on('quizzes')->onDelete('cascade');
        
        $table->integer('score');       
        $table->integer('total_questions'); 
        $table->timestamp('completed_at')->useCurrent();
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
