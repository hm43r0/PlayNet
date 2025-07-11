<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // subscriber
            $table->foreignId('channel_id')->constrained('users')->onDelete('cascade'); // channel owner
            $table->timestamps();
            $table->unique(['user_id', 'channel_id']);
        });
    }
    public function down() {
        Schema::dropIfExists('subscriptions');
    }
};
