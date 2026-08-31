<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemberTeam extends Model
{
    protected $fillable = [
        'member_id',
        'team_id',
        'joined_date',
        'left_date',
        'notes',
    ];

    protected $casts = [
        'joined_date' => 'date',
        'left_date' => 'date',
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
        return $query->whereNull('left_date');
    }
}
