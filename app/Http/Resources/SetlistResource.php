<?php

namespace App\Http\Resources;

use App\Models\Setlist;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Setlist
 */
class SetlistResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'description' => $this->description,
            'songs_count' => $this->whenCounted('songs'),
            'songs' => $this->whenLoaded('songs', fn () => $this->songs->map(fn ($s) => [
                'id' => $s->id,
                'position' => $s->pivot->position,
                'title' => $s->title,
                'title_original' => $s->title_original,
                'origin_group' => $s->origin_group,
                'single' => $s->single ? [
                    'id' => $s->single->id,
                    'code' => $s->single->code,
                    'title' => $s->single->title,
                ] : null,
                'preview_url' => $s->preview_url,
            ])),
        ];
    }
}
