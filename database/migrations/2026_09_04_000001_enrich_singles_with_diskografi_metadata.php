<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('singles', function (Blueprint $table) {
            $table->string('title_jp')->nullable()->after('title');
            $table->string('origin_group', 32)->nullable()->after('title_jp')
                ->comment('AKB48, SKE48, HKT48, NMB48, NGT48, Original');
            $table->integer('release_year')->nullable()->after('release_date');
            $table->string('mv_title')->nullable()->after('notes');
            $table->text('mv_url')->nullable()->after('mv_title');
            $table->text('cover_art_url')->nullable()->after('mv_url');
            $table->string('audio_file')->nullable()->after('cover_art_url');
        });
    }

    public function down(): void
    {
        Schema::table('singles', function (Blueprint $table) {
            $table->dropColumn([
                'title_jp',
                'origin_group',
                'release_year',
                'mv_title',
                'mv_url',
                'cover_art_url',
                'audio_file',
            ]);
        });
    }
};
