<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('map_polygon_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('polygon_layer_id')->constrained('map_polygon_layers')->cascadeOnDelete();
            $table->string('key', 128);
            $table->text('value')->nullable();
            $table->unique(['polygon_layer_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('map_polygon_settings');
    }
};
