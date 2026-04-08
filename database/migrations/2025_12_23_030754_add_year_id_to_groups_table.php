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
    Schema::table('groups', function (Blueprint $table) {
        // We use unsignedInteger because your Year migration used increments()
        $table->unsignedInteger('year_id')->after('id')->nullable();
        
        // Linking the foreign key
        $table->foreign('year_id')->references('id')->on('years')->onDelete('cascade');
    });
}

public function down(): void
{
    Schema::table('groups', function (Blueprint $table) {
        $table->dropForeign(['year_id']);
        $table->dropColumn('year_id');
    });
}
};
