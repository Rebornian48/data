<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Single extends Model
{
    protected $fillable = [
        'code',
        'title',
        'title_jp',
        'origin_group',
        'release_date',
        'release_year',
        'sequence',
        'notes',
        'mv_title',
        'mv_url',
        'cover_art_url',
        'audio_file',
    ];

    protected $casts = [
        'release_date' => 'date',
        'release_year' => 'integer',
        'sequence' => 'integer',
    ];

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'member_singles')
            ->withPivot(['role', 'position'])
            ->withTimestamps();
    }

    public function senbatsuCount(): int
    {
        return $this->members()->count();
    }

    public function centers(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'member_singles')
            ->wherePivot('role', 'center')
            ->withPivot(['role', 'position']);
    }

    public function songs(): HasMany
    {
        return $this->hasMany(Song::class)->orderBy('external_id');
    }

    public function couplingSongs(): HasMany
    {
        return $this->hasMany(CouplingSong::class)->orderBy('id');
    }
}
