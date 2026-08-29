<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MapPoint extends Model
{
    protected $fillable = [
        'map_id', 'group', 'marker_icon', 'marker_color', 'icon_color',
        'custom_size', 'name', 'image', 'description', 'location',
        'latitude', 'longitude', 'extras', 'sort',
    ];

    protected $casts = [
        'extras' => 'array',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function map(): BelongsTo
    {
        return $this->belongsTo(Map::class);
    }
}
