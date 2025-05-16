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
        Schema::create('chat_threads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entrepreneur_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('visitor_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['entrepreneur_id', 'visitor_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
