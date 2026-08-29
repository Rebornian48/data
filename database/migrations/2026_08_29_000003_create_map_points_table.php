<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('map_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('map_id')->constrained('maps')->cascadeOnDelete();
            $table->string('group', 128)->nullable();
            $table->string('marker_icon', 128)->nullable();
            $table->string('marker_color', 64)->nullable();
            $table->string('icon_color', 64)->nullable();
            $table->string('custom_size', 32)->nullable();
            $table->string('name');
            $table->string('image', 512)->nullable();
            $table->text('description')->nullable();
            $table->string('location', 512)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->json('extras')->nullable();
            $table->integer('sort')->default(0);
            $table->timestamps();

            $table->index(['map_id', 'group']);
            $table->index(['latitude', 'longitude']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('map_points');
    }
};
