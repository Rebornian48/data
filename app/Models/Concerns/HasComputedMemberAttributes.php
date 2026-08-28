<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasComputedMemberAttributes
{
    protected function effectiveJoinDate(): Attribute
    {
        return Attribute::get(fn () => $this->join_date ?? $this->generation?->join_date);
    }

    protected function currentAge(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->birth_date) {
                return null;
            }

            return round($this->birth_date->diffInDays(now()) / 365.25, 2);
        });
    }

    protected function ageAtGraduation(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->birth_date || ! $this->graduation_date) {
                return null;
            }

            return round($this->birth_date->diffInDays($this->graduation_date) / 365.25, 2);
        });
    }

    protected function ageAtJoin(): Attribute
    {
        return Attribute::get(function () {
            $join = $this->effective_join_date;
            if (! $this->birth_date || ! $join) {
                return null;
            }

            return $this->birth_date->diffInYears($join);
        });
    }

    protected function daysInJkt48(): Attribute
    {
        return Attribute::get(function () {
            $join = $this->effective_join_date;
            if (! $join) {
                return null;
            }
            $endDate = $this->graduation_date ?? now();

            return $join->diffInDays($endDate);
        });
    }

    protected function yearsInJkt48(): Attribute
    {
        return Attribute::get(function () {
            $days = $this->days_in_jkt48;

            return $days ? round($days / 365.25, 1) : null;
        });
    }

    public function totalSenbatsu(): int
    {
        return $this->singles()->count();
    }

    public function totalCenter(): int
    {
        return $this->centerSingles()->count();
    }
}
