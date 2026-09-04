<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupling_songs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('single_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('title_jp')->nullable();
            $table->string('origin_group', 32)->nullable();
            $table->integer('release_year')->nullable();
            $table->string('mv_title')->nullable();
            $table->text('mv_url')->nullable();
            $table->string('audio_file')->nullable();
            $table->timestamps();

            $table->index('single_id');
        });

        Schema::create('coupling_song_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupling_song_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->enum('role', ['member', 'center'])->default('member');
            $table->integer('position')->nullable();
            $table->timestamps();

            $table->unique(['coupling_song_id', 'member_id']);
            $table->index('role');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupling_song_members');
        Schema::dropIfExists('coupling_songs');
    }
};
