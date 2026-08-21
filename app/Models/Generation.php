<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Generation extends Model
{
    protected $fillable = [
        'code',
        'name',
        'announcement_date',
        'join_date',
        'description',
    ];

    protected $casts = [
        'announcement_date' => 'date',
        'join_date' => 'date',
    ];

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    public function activeMembers(): HasMany
    {
        return $this->hasMany(Member::class)->where('status', 'Aktif');
    }

    public function graduatedMembers(): HasMany
    {
        return $this->hasMany(Member::class)->where('status', 'Lulus');
    }

    /**
     * Total days since generation started.
     */
    public function daysSinceStart(): ?int
    {
        return $this->join_date?->diffInDays(now());
    }
}
