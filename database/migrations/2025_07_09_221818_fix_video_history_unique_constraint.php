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
        // Try to drop the constraint, but ignore errors if it doesn't exist
        try {
            Schema::table('video_history', function (Blueprint $table) {
                $table->dropUnique(['user_id', 'video_id', 'created_at']);
            });
        } catch (\Exception $e) {
            // Constraint might not exist or be named differently, that's okay
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('video_history', function (Blueprint $table) {
            // Restore the original unique constraint
            $table->unique(['user_id', 'video_id', 'created_at']);
        });
    }
};
