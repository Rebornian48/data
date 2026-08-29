<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MapPolygonLayer extends Model
{
    protected $fillable = ['map_id', 'name', 'sort'];

    public function map(): BelongsTo
    {
        return $this->belongsTo(Map::class);
    }

    public function settings(): HasMany
    {
        return $this->hasMany(MapPolygonSetting::class, 'polygon_layer_id');
    }

    public function settingsMap(): array
    {
        return $this->settings->pluck('value', 'key')->all();
    }
}
