<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Single extends Model
{
    protected $fillable = [
        'code',
        'title',
        'release_date',
        'sequence',
        'notes',
    ];

    protected $casts = [
        'release_date' => 'date',
        'sequence' => 'integer',
    ];

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'member_singles')
            ->withPivot(['role', 'position'])
            ->withTimestamps();
    }

    public function senbatsuCount(): int
    {
        return $this->members()->count();
    }

    public function centers(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'member_singles')
            ->wherePivot('role', 'center')
            ->withPivot(['role', 'position']);
    }
}
