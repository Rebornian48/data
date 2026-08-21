<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('nickname', 100)->nullable();
            $table->string('birth_place', 150)->nullable();
            $table->date('birth_date')->nullable();
            $table->foreignId('generation_id')->constrained()->restrictOnDelete();

            // Career timeline
            $table->date('join_date')->nullable();
            $table->date('cancelled_date')->nullable();
            $table->date('rejoin_date')->nullable();
            $table->date('promotion_date')->nullable();
            $table->date('graduation_announce_date')->nullable();
            $table->string('graduation_announce_event')->nullable();
            $table->date('graduation_date')->nullable();

            // Status
            $table->enum('status', ['Aktif', 'Lulus'])->default('Aktif');
            $table->string('restructure_status', 100)->nullable();

            // Media
            $table->string('photo_url', 500)->nullable();
            $table->text('bio')->nullable();

            $table->timestamps();

            $table->index('name');
            $table->index('status');
            $table->index('join_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
