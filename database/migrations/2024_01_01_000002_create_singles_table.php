<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('singles', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique()->comment('S1, S2, S19 (EK), etc');
            $table->string('title');
            $table->date('release_date')->nullable();
            $table->integer('sequence')->comment('Order for sorting');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('sequence');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('singles');
    }
};
