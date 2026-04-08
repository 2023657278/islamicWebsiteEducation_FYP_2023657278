<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('textbook_progress', function (Blueprint $table) {
            $table->id();

            // ✅ TRY 3: Unsigned Integer (Common in older Laravel projects)
            $table->unsignedInteger('user_id'); 
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // We keep resources as BigInt (Standard). If this fails next, we change it to unsignedInteger too.
            $table->unsignedInteger('resource_id');
            $table->foreign('resource_id')->references('id')->on('resources')->onDelete('cascade');

            $table->integer('current_page')->default(1);
            $table->integer('total_pages')->default(1);
            $table->float('percentage')->default(0);
            $table->boolean('is_completed')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('textbook_progress');
    }
};