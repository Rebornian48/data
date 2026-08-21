<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('generations', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique()->comment('1-13, V1, V2, Kaigai 1, etc');
            $table->string('name', 100);
            $table->date('announcement_date')->nullable();
            $table->date('join_date')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generations');
    }
};
