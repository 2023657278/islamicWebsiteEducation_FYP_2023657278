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
        Schema::create('resources', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->nullable();
            
            // 1. MAIN CONTENT COLUMN
            // Stores "notes/chapter1.pdf" (for Local) OR "dQw4w9WgXcQ" (for YouTube ID)
            $table->string('file_path'); 

            // 2. RESOURCE TYPE
            // 'note' = PDF/Doc from Local Storage
            // 'video' = Video from YouTube API
            $table->enum('type', ['note', 'video'])->default('note');

            // 3. RELATIONSHIPS
            $table->unsignedInteger('teacher_id');
            $table->unsignedInteger('group_id');
            $table->unsignedInteger('subject_id');
            
            $table->timestamps();

            // 4. FOREIGN KEYS
            // Ensure your 'users', 'groups', and 'subjects' tables use increments() or unsignedInteger id
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('subjects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};