<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('setlists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['regular', 'special'])->default('regular');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['name', 'type']);
            $table->index('type');
        });

        Schema::create('setlist_songs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('setlist_id')->constrained()->cascadeOnDelete();
            $table->foreignId('song_id')->constrained()->cascadeOnDelete();
            $table->integer('position')->nullable();
            $table->timestamps();

            $table->unique(['setlist_id', 'song_id']);
            $table->index('setlist_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setlist_songs');
        Schema::dropIfExists('setlists');
    }
};
