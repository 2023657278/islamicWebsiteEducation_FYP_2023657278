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
    Schema::table('room_participants', function (Blueprint $table) {
        // Only add the columns that ARE NOT in your SQLyog screenshot yet
        $table->integer('skills_locked_turns')->default(0); 
        $table->timestamp('strike_locked_until')->nullable();
    });
}

public function down()
{
    Schema::table('room_participants', function (Blueprint $table) {
        $table->dropColumn(['skills_locked_turns', 'strike_locked_until']);
    });
}
};
