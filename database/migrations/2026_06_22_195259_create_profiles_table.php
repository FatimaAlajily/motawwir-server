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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->text('bio')->nullable();
            $table->string('phone')->nullable();
            $table->string('location')->nullable();
            $table->string('skill')->nullable();
            $table->string('github')->nullable();
            $table->string('gmail')->nullable();
            $table->string('domain')->nullable();
            $table->string('cv')->nullable();
            $table->string('linkedin')->nullable();
            // $table->foreignId('comment_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
