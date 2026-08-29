<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Map extends Model
{
    protected $fillable = ['slug', 'title', 'subtitle', 'google_sheet_id', 'is_published'];

    protected $casts = ['is_published' => 'bool'];

    public function settings(): HasMany
    {
        return $this->hasMany(MapSetting::class);
    }

    public function points(): HasMany
    {
        return $this->hasMany(MapPoint::class);
    }

    public function polylines(): HasMany
    {
        return $this->hasMany(MapPolyline::class);
    }

    public function polygonLayers(): HasMany
    {
        return $this->hasMany(MapPolygonLayer::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(MapNote::class);
    }

    /** Options as flat [key => value]. */
    public function settingsMap(): array
    {
        return $this->settings->pluck('value', 'key')->all();
    }
}
