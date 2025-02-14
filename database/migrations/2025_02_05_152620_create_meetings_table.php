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
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->string('identifier')->index();
            $table->string('private_key');
            $table->string('public_key');
            $table->text('name');
            $table->text('date_from');
            $table->text('date_to');
            $table->text('time_start');
            $table->text('time_end');
            $table->datetime('destroy_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meetings');
    }
};
