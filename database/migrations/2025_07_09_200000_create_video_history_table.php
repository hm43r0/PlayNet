<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('video_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('video_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->timestamp('watched_at')->nullable();
            $table->unique(['user_id', 'video_id', 'created_at']); // Prevent duplicate for same video at same time
        });
    }
    public function down() {
        Schema::dropIfExists('video_history');
    }
};
