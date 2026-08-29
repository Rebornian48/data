<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MapPolygonSetting extends Model
{
    public $timestamps = false;

    protected $fillable = ['polygon_layer_id', 'key', 'value'];

    public function layer(): BelongsTo
    {
        return $this->belongsTo(MapPolygonLayer::class, 'polygon_layer_id');
    }
}
