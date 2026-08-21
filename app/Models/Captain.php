<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Captain extends Model
{
    protected $fillable = [
        'member_id',
        'position',
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

    public function scopeActive($query)
    {
        return $query->whereNull('end_date');
    }

    public function scopePosition($query, string $position)
    {
        return $query->where('position', $position);
    }

    /**
     * Duration in days (using end_date or today).
     */
    protected function durationDays(): Attribute
    {
        return Attribute::get(fn () => $this->start_date->diffInDays($this->end_date ?? now()));
    }
}
