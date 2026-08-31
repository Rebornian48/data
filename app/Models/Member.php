<?php

namespace App\Models;

use App\Models\Concerns\HasComputedMemberAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    use HasComputedMemberAttributes;

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

    public function teamHistory(): HasMany
    {
        return $this->hasMany(MemberTeam::class)->orderBy('joined_date');
    }

    public function currentTeams(): HasMany
    {
        return $this->hasMany(MemberTeam::class)->whereNull('left_date');
    }

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
        if (! $term) {
            return $query;
        }

        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('nickname', 'like', "%{$term}%")
                ->orWhere('birth_place', 'like', "%{$term}%");
        });
    }

    protected static function booted(): void
    {
        static::saving(function (Member $m) {
            if ($m->graduation_date && $m->graduation_date->lte(now())) {
                $m->status = 'Lulus';
            }
        });
    }
}
