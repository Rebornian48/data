<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('map_polygon_layers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('map_id')->constrained('maps')->cascadeOnDelete();
            $table->string('name', 128);
            $table->integer('sort')->default(0);
            $table->timestamps();
            $table->unique(['map_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('map_polygon_layers');
    }
};
