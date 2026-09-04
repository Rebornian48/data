<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubUnit extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    public function songs(): HasMany
    {
        return $this->hasMany(SubUnitSong::class)->orderBy('debut_date');
    }
}
