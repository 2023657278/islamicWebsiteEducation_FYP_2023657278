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
    Schema::table('questions', function (Blueprint $table) {
        // 🟢 Add the missing subject_id (Foreign Key)
        if (!Schema::hasColumn('questions', 'subject_id')) {
            $table->unsignedBigInteger('subject_id')->nullable()->after('id');
        }

        // 🟢 Ensure difficulty exists and is a string (Easy, Medium, Hard)
        if (!Schema::hasColumn('questions', 'difficulty')) {
            $table->string('difficulty')->nullable()->after('subject_id');
        }
    });
}

public function down()
{
    Schema::table('questions', function (Blueprint $table) {
        $table->dropColumn(['subject_id', 'difficulty']);
    });
}
};
