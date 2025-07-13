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
        Schema::create('analytics_entry_tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('analytics_entry_id')->constrained('analytics_entries')->onDelete('cascade');
            $table->foreignId('analytics_tag_id')->constrained('analytics_tags')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('analytics_entry_tags');
    }
};
