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
    // 1. MODIFY RESOURCES (For General Notebooks)
    Schema::table('resources', function (Blueprint $table) {
        // 0 = Private (Class only), 1 = Public (General Note)
        $table->boolean('is_public')->default(false)->after('id');
        $table->string('subject_tag')->nullable()->after('is_public'); // e.g. "Math Form 4"
    });

    // 2. CREATE BOOKMARKS (For Students to save notes)
    Schema::create('bookmarks', function (Blueprint $table) {
        $table->id();
        $table->unsignedInteger('user_id');      
        $table->unsignedInteger('resource_id');  
        $table->timestamps();
        // Foreign Keys
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        $table->foreign('resource_id')->references('id')->on('resources')->onDelete('cascade');
    });

    // 3. EXAM PAPERS (The Past Year Paper Title)
    Schema::create('exam_papers', function (Blueprint $table) {
        $table->id();
        $table->string('title');       // e.g., "SPM Mathematics 2023"
        $table->string('year');        // e.g., "2023"
        $table->string('subject');     // e.g., "History"
        $table->timestamps();
    });

    // 4. EXAM QUESTIONS (Linked to the Paper)
    Schema::create('exam_questions', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('exam_paper_id');
        $table->text('question_text');
        
        // Simple Options
        $table->string('option_a');
        $table->string('option_b');
        $table->string('option_c');
        $table->string('option_d');
        
        $table->string('correct_option'); // 'option_a', 'option_b', etc.
        $table->text('explanation')->nullable(); // For Flashcard mode
        
        $table->timestamps();
        $table->foreign('exam_paper_id')->references('id')->on('exam_papers')->onDelete('cascade');
    });

    // 5. EXAM RESULTS (Student Score History)
    Schema::create('exam_results', function (Blueprint $table) {
        $table->id();
        $table->unsignedInteger('user_id');
        $table->unsignedBigInteger('exam_paper_id');
        $table->integer('score');       // e.g. 80
        $table->integer('total_questions'); 
        $table->timestamps();
        
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        $table->foreign('exam_paper_id')->references('id')->on('exam_papers')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
