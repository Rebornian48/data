<?php

namespace App\Http\Resources;

use App\Models\SubUnit;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin SubUnit
 */
class SubUnitResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'songs_count' => $this->whenCounted('songs'),
            'songs' => $this->whenLoaded('songs', fn () => $this->songs->map(fn ($s) => [
                'id' => $s->id,
                'title' => $s->title,
                'title_original' => $s->title_original,
                'origin_group' => $s->origin_group,
                'debut_date' => optional($s->debut_date)?->toDateString(),
                'debut_at' => $s->debut_at,
                'released' => (bool) $s->released,
                'has_mv' => (bool) $s->has_mv,
                'preview_url' => $s->preview_url,
            ])),
        ];
    }
}
