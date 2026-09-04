<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MvLocation extends Model
{
    protected $fillable = [
        'category',
        'song_title',
        'song_title_jp',
        'release_year',
        'location',
        'position',
        'latitude',
        'longitude',
        'notes',
    ];

    protected $casts = [
        'release_year' => 'integer',
        'position' => 'integer',
        'latitude' => 'decimal:6',
        'longitude' => 'decimal:6',
    ];
}
