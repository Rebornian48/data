<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubUnitSong extends Model
{
    protected $fillable = [
        'sub_unit_id',
        'title',
        'title_original',
        'origin_group',
        'debut_date',
        'debut_at',
        'released',
        'has_mv',
        'preview_url',
    ];

    protected $casts = [
        'debut_date' => 'date',
        'released' => 'boolean',
        'has_mv' => 'boolean',
    ];

    public function subUnit(): BelongsTo
    {
        return $this->belongsTo(SubUnit::class);
    }
}
