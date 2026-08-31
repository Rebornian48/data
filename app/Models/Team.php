<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    protected $fillable = [
        'code',
        'name',
        'color',
        'formed_at',
        'disbanded_at',
        'notes',
    ];

    protected $casts = [
        'formed_at' => 'date',
        'disbanded_at' => 'date',
    ];

    public function captains(): HasMany
    {
        return $this->hasMany(Captain::class);
    }

    public function memberships(): HasMany
    {
        return $this->hasMany(MemberTeam::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'member_teams')
            ->withPivot(['joined_date', 'left_date', 'notes'])
            ->withTimestamps();
    }

    public function currentMembers(): BelongsToMany
    {
        return $this->members()->wherePivotNull('left_date');
    }
}
