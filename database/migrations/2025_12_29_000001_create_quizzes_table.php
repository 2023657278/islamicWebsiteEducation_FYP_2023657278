<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->increments('id');           // Creates an UNSIGNED INTEGER
            $table->string('title');
            $table->text('description')->nullable(); 
            $table->integer('duration_minutes')->default(30);
            
            // --- FIX START ---
            // Instead of 'foreignId' (which is BigInt), we use 'unsignedInteger' (to match increments)
            
            // 1. Teacher
            $table->unsignedInteger('teacher_id');
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('cascade');

            // 2. Group
            $table->unsignedInteger('group_id');
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');

            // 3. Subject
            $table->unsignedInteger('subject_id');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
            // --- FIX END ---
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};