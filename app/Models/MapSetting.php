<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MapSetting extends Model
{
    public $timestamps = false;

    protected $fillable = ['map_id', 'key', 'value'];

    public function map(): BelongsTo
    {
        return $this->belongsTo(Map::class);
    }
}
