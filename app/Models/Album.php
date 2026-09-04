<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Album extends Model
{
    protected $fillable = [
        'code',
        'type',
        'title',
        'title_jp',
        'sequence',
        'release_date',
        'cover_url',
    ];

    protected $casts = [
        'release_date' => 'date',
        'sequence' => 'integer',
    ];

    public function tracks(): HasMany
    {
        return $this->hasMany(AlbumTrack::class)->orderBy('position');
    }

    public function scopeAlbums($query)
    {
        return $query->where('type', 'album');
    }

    public function scopeEps($query)
    {
        return $query->where('type', 'ep');
    }
}
