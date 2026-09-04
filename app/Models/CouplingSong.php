<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CouplingSong extends Model
{
    protected $fillable = [
        'single_id',
        'title',
        'title_jp',
        'origin_group',
        'release_year',
        'mv_title',
        'mv_url',
        'audio_file',
    ];

    protected $casts = [
        'release_year' => 'integer',
    ];

    public function single(): BelongsTo
    {
        return $this->belongsTo(Single::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'coupling_song_members')
            ->withPivot(['role', 'position'])
            ->withTimestamps();
    }

    public function centers(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'coupling_song_members')
            ->wherePivot('role', 'center')
            ->withPivot(['role', 'position']);
    }
}
