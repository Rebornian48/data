<?php

namespace App\Http\Resources;

use App\Models\Single;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Single
 */
class SingleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'code'         => $this->code,
            'title'        => $this->title,
            'release_date' => optional($this->release_date)?->toDateString(),
            'sequence'     => $this->sequence,
            'notes'        => $this->notes,
            'members'      => $this->whenLoaded('members', fn () => $this->members
                ->map(fn ($m) => [
                    'id'       => $m->id,
                    'name'     => $m->name,
                    'nickname' => $m->nickname,
                    'role'     => $m->pivot->role ?? null,
                    'position' => $m->pivot->position ?? null,
                ])
            ),
        ];
    }
}
