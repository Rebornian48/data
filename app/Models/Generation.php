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

    /**
     * Whenever a generation's join_date changes, backfill member.join_date
     * for every member of that generation whose column is still null.
     * Explicitly-set member join_date is left untouched.
     */
    protected static function booted(): void
    {
        static::saved(function (Generation $gen) {
            if (! $gen->join_date) {
                return;
            }
            if (! $gen->wasChanged('join_date') && ! $gen->wasRecentlyCreated) {
                return;
            }

            Member::where('generation_id', $gen->id)
                ->whereNull('join_date')
                ->update(['join_date' => $gen->join_date]);
        });
    }
}
