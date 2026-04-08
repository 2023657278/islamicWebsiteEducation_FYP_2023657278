<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('sender_id'); // Admin who sent it
            
            // Type: 'global', 'group', 'private'
            $table->string('type'); 
            
            // If type='group', this is group_id. 
            // If type='private', this is user_id (student/teacher). 
            // If type='global', this is null.
            $table->unsignedInteger('target_id')->nullable(); 

            $table->string('subject');
            $table->text('message');
            $table->timestamps();

            // Foreign keys (optional, but good for data integrity)
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('messages');
    }
};