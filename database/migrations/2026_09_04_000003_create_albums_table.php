<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('albums', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique()->comment('album-1, ep-1, dst.');
            $table->enum('type', ['album', 'ep'])->default('album');
            $table->string('title');
            $table->string('title_jp')->nullable();
            $table->integer('sequence');
            $table->date('release_date')->nullable();
            $table->text('cover_url')->nullable();
            $table->timestamps();

            $table->index(['type', 'sequence']);
        });

        Schema::create('album_tracks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('album_id')->constrained()->cascadeOnDelete();
            $table->foreignId('song_id')->nullable()->constrained('songs')->nullOnDelete();
            $table->integer('position');
            $table->string('title')->comment('Judul di tracklist (ID)');
            $table->timestamps();

            $table->index(['album_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('album_tracks');
        Schema::dropIfExists('albums');
    }
};
