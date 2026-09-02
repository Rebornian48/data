<?php

namespace App\Http\Resources;

use App\Models\Captain;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Captain
 */
class CaptainResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'member_id'     => $this->member_id,
            'team_id'       => $this->team_id,
            'role'          => $this->role,
            'position'      => $this->position,
            'start_date'    => optional($this->start_date)?->toDateString(),
            'end_date'      => optional($this->end_date)?->toDateString(),
            'duration_days' => $this->duration_days,
            'notes'         => $this->notes,
            'member'        => $this->whenLoaded('member', fn () => [
                'id'       => $this->member->id,
                'name'     => $this->member->name,
                'nickname' => $this->member->nickname,
            ]),
            'team' => $this->whenLoaded('team', fn () => $this->team ? [
                'id'   => $this->team->id,
                'code' => $this->team->code,
                'name' => $this->team->name,
            ] : null),
        ];
    }
}
