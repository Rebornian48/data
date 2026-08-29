<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('map_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('map_id')->constrained('maps')->cascadeOnDelete();
            $table->string('key', 128);
            $table->text('value')->nullable();
            $table->unique(['map_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('map_settings');
    }
};
