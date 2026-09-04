<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Song extends Model
{
    protected $fillable = [
        'external_id',
        'title',
        'title_original',
        'origin_group',
        'single_id',
        'single_ref_raw',
        'other_compilations',
        'setlist',
        'special_setlist',
        'debut_date',
        'debut_at',
        'released',
        'preview_url',
        'mv_title',
    ];

    protected $casts = [
        'debut_date' => 'date',
        'released' => 'boolean',
        'external_id' => 'integer',
    ];

    public function single(): BelongsTo
    {
        return $this->belongsTo(Single::class);
    }

    public function setlists(): BelongsToMany
    {
        return $this->belongsToMany(Setlist::class, 'setlist_songs')
            ->withPivot('position')
            ->withTimestamps();
    }

    public function scopeReleased($query, bool $released = true)
    {
        return $query->where('released', $released);
    }

    public function scopeOriginal($query)
    {
        return $query->where('origin_group', 'Original');
    }

    public function scopeFromGroup($query, string $group)
    {
        return $query->where('origin_group', $group);
    }
}
