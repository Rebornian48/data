<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('songs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('external_id')
                ->comment('ID asli dari spreadsheet Diskografi (Semua Lagu) — tidak unik: ada 2 pasang ID duplikat di sumber');
            // NB: bukan unique — spreadsheet punya beberapa ID kembar (mis. 220, 228)
            $table->string('title');
            $table->string('title_original')->nullable()
                ->comment('Judul asli (JP untuk cover, sama untuk original)');
            $table->string('origin_group', 32)->nullable();
            $table->foreignId('single_id')->nullable()->constrained()->nullOnDelete();
            $table->string('single_ref_raw')->nullable()
                ->comment('Nama single dari spreadsheet — dipertahankan bila mapping ambigu');
            $table->string('other_compilations')->nullable();
            $table->string('setlist')->nullable();
            $table->string('special_setlist')->nullable();
            $table->date('debut_date')->nullable();
            $table->string('debut_at')->nullable();
            $table->boolean('released')->default(true);
            $table->text('preview_url')->nullable()
                ->comment('YouTube preview untuk Request Hour');
            $table->string('mv_title')->nullable();
            $table->timestamps();

            $table->index('external_id');
            $table->index('single_id');
            $table->index('origin_group');
            $table->index('released');
            $table->index('debut_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('songs');
    }
};
