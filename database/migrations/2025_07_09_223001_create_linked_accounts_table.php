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
        Schema::create('linked_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('primary_user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('linked_user_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('linked_at')->nullable();
            $table->timestamps();
            
            // Prevent duplicate links
            $table->unique(['primary_user_id', 'linked_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('linked_accounts');
    }
};
