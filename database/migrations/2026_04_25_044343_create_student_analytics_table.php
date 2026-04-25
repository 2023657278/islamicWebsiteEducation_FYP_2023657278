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
        Schema::create('student_analytics', function (Blueprint $table) {
            $table->increments('id');
        $table->unsignedInteger('user_id');
        $table->decimal('current_slope', 5, 2)->default(0.00); // Tracks improvement rate
        $table->enum('status', ['Excellent', 'Improving', 'Stable', 'Warning', 'Critical'])->default('Stable');
        $table->timestamp('last_calculated_at')->nullable();
        $table->timestamps();

        // Foreign Key link to your users
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
    });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_analytics');
    }
};
