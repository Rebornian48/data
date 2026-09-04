<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mv_locations', function (Blueprint $table) {
            $table->id();
            $table->string('category')->nullable()->comment('Single / Album Studio / Sub Unit / dsb.');
            $table->string('song_title');
            $table->string('song_title_jp')->nullable();
            $table->integer('release_year')->nullable();
            $table->string('location');
            $table->integer('position')->default(1)
                ->comment('1-based bila 1 MV punya beberapa lokasi');
            $table->decimal('latitude', 10, 6)->nullable();
            $table->decimal('longitude', 10, 6)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['song_title', 'position']);
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mv_locations');
    }
};
