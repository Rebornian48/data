<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Member extends Model
{
    protected $fillable = [
        'name',
        'nickname',
        'birth_place',
        'birth_date',
        'generation_id',
        'join_date',
        'cancelled_date',
        'rejoin_date',
        'promotion_date',
        'graduation_announce_date',
        'graduation_announce_event',
        'graduation_date',
        'status',
        'restructure_status',
        'photo_url',
        'bio',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'join_date' => 'date',
        'cancelled_date' => 'date',
        'rejoin_date' => 'date',
        'promotion_date' => 'date',
        'graduation_announce_date' => 'date',
        'graduation_date' => 'date',
    ];

    // ------------------------------------------------------------------
    // Relationships
    // ------------------------------------------------------------------

    public function generation(): BelongsTo
    {
        return $this->belongsTo(Generation::class);
    }

    public function singles(): BelongsToMany
    {
        return $this->belongsToMany(Single::class, 'member_singles')
            ->withPivot(['role', 'position'])
            ->withTimestamps();
    }

    public function centerSingles(): BelongsToMany
    {
        return $this->belongsToMany(Single::class, 'member_singles')
            ->wherePivot('role', 'center');
    }

    public function captains(): HasMany
    {
        return $this->hasMany(Captain::class);
    }

    // ------------------------------------------------------------------
    // Scopes
    // ------------------------------------------------------------------

    public function scopeActive($query)
    {
        return $query->where('status', 'Aktif');
    }

    public function scopeGraduated($query)
    {
        return $query->where('status', 'Lulus');
    }

    public function scopeByGeneration($query, $generationId)
    {
        return $query->where('generation_id', $generationId);
    }

    public function scopeSearch($query, ?string $term)
    {
        if (! $term) return $query;
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('nickname', 'like', "%{$term}%")
              ->orWhere('birth_place', 'like', "%{$term}%");
        });
    }

    // ------------------------------------------------------------------
    // Model events
    // ------------------------------------------------------------------

    /**
     * Auto-sync status = 'Lulus' when graduation_date has arrived (or passed).
     * Members whose graduation_date is in the future keep their existing status
     * (typically Aktif with graduation_announce_date filled).
     */
    protected static function booted(): void
    {
        static::saving(function (Member $m) {
            if ($m->graduation_date && $m->graduation_date->lte(now())) {
                $m->status = 'Lulus';
            }
        });
    }

    // ------------------------------------------------------------------
    // Computed accessors
    // ------------------------------------------------------------------

    /**
     * Effective join date — falls back to the member's generation join_date
     * when the member row itself has none.
     */
    protected function effectiveJoinDate(): Attribute
    {
        return Attribute::get(function () {
            return $this->join_date ?? $this->generation?->join_date;
        });
    }

    /**
     * Current age in years (or age at graduation for graduated members).
     */
    protected function currentAge(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->birth_date) return null;
            $endDate = $this->status === 'Lulus' && $this->graduation_date
                ? $this->graduation_date
                : now();
            return round($this->birth_date->diffInDays($endDate) / 365.25, 2);
        });
    }

    /**
     * Age when joining JKT48.
     */
    protected function ageAtJoin(): Attribute
    {
        return Attribute::get(function () {
            $join = $this->effective_join_date;
            if (! $this->birth_date || ! $join) return null;
            return $this->birth_date->diffInYears($join);
        });
    }

    /**
     * Total days as a JKT48 member.
     */
    protected function daysInJkt48(): Attribute
    {
        return Attribute::get(function () {
            $join = $this->effective_join_date;
            if (! $join) return null;
            $endDate = $this->graduation_date ?? now();
            return $join->diffInDays($endDate);
        });
    }

    /**
     * Number of years as a JKT48 member (rounded).
     */
    protected function yearsInJkt48(): Attribute
    {
        return Attribute::get(function () {
            $days = $this->days_in_jkt48;
            return $days ? round($days / 365.25, 1) : null;
        });
    }

    /**
     * Total senbatsu appearances.
     */
    public function totalSenbatsu(): int
    {
        return $this->singles()->count();
    }

    /**
     * Total times as center.
     */
    public function totalCenter(): int
    {
        return $this->centerSingles()->count();
    }
}
