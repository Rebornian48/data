<?php

namespace App\Http\Resources;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Team
 */
class TeamResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'code'                  => $this->code,
            'name'                  => $this->name,
            'color'                 => $this->color,
            'formed_at'             => optional($this->formed_at)?->toDateString(),
            'disbanded_at'          => optional($this->disbanded_at)?->toDateString(),
            'notes'                 => $this->notes,
            'current_members_count' => $this->when(
                isset($this->current_members_count),
                fn () => (int) $this->current_members_count
            ),
            'current_members'       => $this->whenLoaded('currentMembers', fn () => $this->currentMembers
                ->map(function ($m) {
                    $joined = $m->pivot->joined_date ?? null;
                    if ($joined && ! $joined instanceof \DateTimeInterface) {
                        try {
                            $joined = \Illuminate\Support\Carbon::parse($joined);
                        } catch (\Throwable $e) {
                            $joined = null;
                        }
                    }
                    return [
                        'id'          => $m->id,
                        'name'        => $m->name,
                        'nickname'    => $m->nickname,
                        'status'      => $m->status,
                        'joined_date' => $joined?->toDateString(),
                    ];
                })
            ),
            'captains' => $this->whenLoaded('captains', fn () => $this->captains
                ->map(fn ($c) => [
                    'id'         => $c->id,
                    'member_id'  => $c->member_id,
                    'role'       => $c->role,
                    'start_date' => optional($c->start_date)?->toDateString(),
                    'end_date'   => optional($c->end_date)?->toDateString(),
                ])
            ),
        ];
    }
}
