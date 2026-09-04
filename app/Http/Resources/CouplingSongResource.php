<?php

namespace App\Http\Resources;

use App\Models\CouplingSong;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin CouplingSong
 */
class CouplingSongResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'single_id' => $this->single_id,
            'single' => $this->whenLoaded('single', fn () => $this->single ? [
                'id' => $this->single->id,
                'code' => $this->single->code,
                'title' => $this->single->title,
            ] : null),
            'title' => $this->title,
            'title_jp' => $this->title_jp,
            'origin_group' => $this->origin_group,
            'release_year' => $this->release_year,
            'mv_title' => $this->mv_title,
            'mv_url' => $this->mv_url,
            'audio_file' => $this->audio_file,
            'members' => $this->whenLoaded('members', fn () => $this->members
                ->sortBy('pivot.position')
                ->values()
                ->map(fn ($m) => [
                    'id' => $m->id,
                    'name' => $m->name,
                    'nickname' => $m->nickname,
                    'role' => $m->pivot->role,
                    'position' => $m->pivot->position,
                ])),
        ];
    }
}
