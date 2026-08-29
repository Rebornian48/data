<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MapNote extends Model
{
    protected $fillable = ['map_id', 'body', 'sort'];

    public function map(): BelongsTo
    {
        return $this->belongsTo(Map::class);
    }
}
