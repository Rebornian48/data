<?php

namespace App\Http\Resources;

use App\Models\Generation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Generation
 */
class GenerationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'code'              => $this->code,
            'name'              => $this->name,
            'announcement_date' => optional($this->announcement_date)?->toDateString(),
            'join_date'         => optional($this->join_date)?->toDateString(),
            'description'       => $this->description,
            'members_count'     => $this->when(isset($this->members_count), fn () => (int) $this->members_count),
            'active_count'      => $this->when(isset($this->active_members_count), fn () => (int) $this->active_members_count),
            'graduated_count'   => $this->when(isset($this->graduated_members_count), fn () => (int) $this->graduated_members_count),
            'members'           => MemberResource::collection($this->whenLoaded('members')),
        ];
    }
}
