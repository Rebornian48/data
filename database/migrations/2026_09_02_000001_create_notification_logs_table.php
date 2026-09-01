<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->string('event_type', 32);
            $table->unsignedBigInteger('member_id')->nullable();
            $table->date('event_date');
            $table->string('channel', 16);
            $table->timestamp('sent_at')->useCurrent();
            $table->unique(['event_type', 'member_id', 'event_date', 'channel'], 'notif_dedupe');
            $table->index(['event_date', 'channel']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
