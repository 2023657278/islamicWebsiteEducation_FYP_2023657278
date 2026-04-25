<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->increments('id'); // bigint unsigned NOT NULL AUTO_INCREMENT
            $table->unsignedInteger('sender_id'); // int unsigned NOT NULL
            $table->string('type'); // varchar(255) NOT NULL
            $table->unsignedInteger('target_id')->nullable(); // int unsigned DEFAULT NULL
            $table->string('subject'); // varchar(255) NOT NULL
            $table->text('message'); // text NOT NULL
            $table->timestamps(); // created_at & updated_at

            // Foreign Key
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('messages');
    }
};