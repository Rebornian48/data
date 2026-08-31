<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Captain extends Model
{
    protected $fillable = [
        'member_id',
        'team_id',
        'role',
        'start_date',
        'end_date',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function scopeActive($query)
    {
        return $query->whereNull('end_date');
    }

    public function scopeRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    public function scopeForTeam($query, ?int $teamId)
    {
        return $teamId === null
            ? $query->whereNull('team_id')
            : $query->where('team_id', $teamId);
    }

    protected function position(): Attribute
    {
        return Attribute::get(function () {
            $role = $this->role ?: 'Kapten';
            if (! $this->team_id) {
                return "{$role} JKT48";
            }
            $team = $this->relationLoaded('team') ? $this->team : $this->team()->first();
            return $team ? "{$role} Tim {$team->code}" : $role;
        });
    }

    protected function durationDays(): Attribute
    {
        return Attribute::get(fn () => $this->start_date->diffInDays($this->end_date ?? now()));
    }
}
