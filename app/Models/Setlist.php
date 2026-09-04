<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Setlist extends Model
{
    protected $fillable = [
        'name',
        'type',
        'description',
    ];

    public function songs(): BelongsToMany
    {
        return $this->belongsToMany(Song::class, 'setlist_songs')
            ->withPivot('position')
            ->orderBy('setlist_songs.position')
            ->withTimestamps();
    }

    public function scopeRegular($query)
    {
        return $query->where('type', 'regular');
    }

    public function scopeSpecial($query)
    {
        return $query->where('type', 'special');
    }
}
