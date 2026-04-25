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
        $table->boolean('is_public')->default(false);
        $table->string('subject_tag')->nullable();
        $table->string('title')->nullable();
        $table->text('description')->nullable();
        
        // This is the missing column causing your error!
        $table->string('file_url')->nullable();
        
        $table->enum('type', ['note', 'video', 'textbook'])->default('note');
        $table->string('youtube_video_id', 20)->nullable();
        
        $table->unsignedInteger('teacher_id');
        $table->integer('group_id')->nullable();
        $table->unsignedInteger('subject_id');
        
        $table->timestamps();

        // Foreign Keys
        $table->foreign('teacher_id')->references('id')->on('users')->onDelete('cascade');
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