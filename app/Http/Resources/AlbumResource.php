<?php

namespace App\Http\Resources;

use App\Models\Album;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Album
 */
class AlbumResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'type' => $this->type,
            'title' => $this->title,
            'title_jp' => $this->title_jp,
            'sequence' => $this->sequence,
            'release_date' => optional($this->release_date)?->toDateString(),
            'cover_url' => $this->cover_url,
            'tracks' => $this->whenLoaded('tracks', fn () => $this->tracks->map(fn ($t) => [
                'position' => $t->position,
                'title' => $t->title,
                'song_id' => $t->song_id,
                'song' => $t->song ? [
                    'id' => $t->song->id,
                    'title_original' => $t->song->title_original,
                    'origin_group' => $t->song->origin_group,
                    'preview_url' => $t->song->preview_url,
                ] : null,
            ])),
        ];
    }
}
