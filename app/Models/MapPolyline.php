<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MapPolyline extends Model
{
    protected $fillable = ['map_id', 'display_name', 'geojson_url', 'description', 'color', 'sort'];

    public function map(): BelongsTo
    {
        return $this->belongsTo(Map::class);
    }
}
