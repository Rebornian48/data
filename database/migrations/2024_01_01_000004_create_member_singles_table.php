<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('member_singles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->foreignId('single_id')->constrained()->cascadeOnDelete();
            $table->enum('role', ['member', 'center'])->default('member');
            $table->integer('position')->nullable();
            $table->timestamps();

            $table->unique(['member_id', 'single_id']);
            $table->index('role');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_singles');
    }
};
