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
        Schema::table('playlist_videos', function (Blueprint $table) {
            $table->integer('order_position')->nullable()->after('video_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('playlist_videos', function (Blueprint $table) {
            $table->dropColumn('order_position');
        });
    }
};
