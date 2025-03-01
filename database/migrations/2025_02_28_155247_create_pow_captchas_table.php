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
        Schema::create('pow_captchas', function (Blueprint $table) {
            $table->id();
            $table->json('puzzles_json');
            $table->json('answers_json');
            $table->integer('difficulty');
            $table->integer('length');
            $table->dateTime('solved_at')->nullable();
            $table->string('solved_token')->index()->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pow_captchas');
    }
};
