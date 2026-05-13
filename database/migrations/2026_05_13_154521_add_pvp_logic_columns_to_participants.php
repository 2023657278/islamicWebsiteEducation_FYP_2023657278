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
        // 🟢 Only add 'rank' if it doesn't exist yet
        if (!Schema::hasColumn('room_participants', 'rank')) {
            $table->integer('rank')->nullable()->after('status');
        }
        
        // 🟢 Only add 'frozen_until' if it doesn't exist yet
        if (!Schema::hasColumn('room_participants', 'frozen_until')) {
            $table->timestamp('frozen_until')->nullable()->after('is_frozen');
        }
    });
}

    /**
     * Reverse the migrations.
     */

public function down()
{
    Schema::table('room_participants', function (Blueprint $table) {
        $table->dropColumn(['rank', 'frozen_until']);
    });
}
};
