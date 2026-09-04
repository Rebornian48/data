<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sub_units', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('sub_unit_songs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_unit_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('title_original')->nullable();
            $table->string('origin_group', 32)->nullable();
            $table->date('debut_date')->nullable();
            $table->string('debut_at')->nullable();
            $table->boolean('released')->default(false);
            $table->boolean('has_mv')->default(false);
            $table->text('preview_url')->nullable();
            $table->timestamps();

            $table->index(['sub_unit_id', 'debut_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sub_unit_songs');
        Schema::dropIfExists('sub_units');
    }
};
