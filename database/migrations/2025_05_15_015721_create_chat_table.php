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
        Schema::create('chat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained()->onDelete('cascade');
            $table->foreignId('recipient_id')->constrained()->onDelete('cascade');
            $table->text('message');
            $table->$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat');
    }
};
