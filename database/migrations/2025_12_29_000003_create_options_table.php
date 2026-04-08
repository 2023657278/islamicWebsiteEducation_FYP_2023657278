<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_create_options_table.php

public function up()
{
    Schema::create('options', function (Blueprint $table) {
        $table->increments('id');
        
        // FIX: Match questions.id (Integer)
        $table->unsignedInteger('question_id');
        $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
        
        $table->string('option_text');
        $table->boolean('is_correct')->default(false);
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('options');
    }
};
