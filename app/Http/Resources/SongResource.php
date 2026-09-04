<?php

namespace App\Http\Resources;

use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Song
 */
class SongResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'external_id' => $this->external_id,
            'title' => $this->title,
            'title_original' => $this->title_original,
            'origin_group' => $this->origin_group,
            'single_id' => $this->single_id,
            'single' => $this->whenLoaded('single', fn () => $this->single ? [
                'id' => $this->single->id,
                'code' => $this->single->code,
                'title' => $this->single->title,
            ] : null),
            'single_ref_raw' => $this->single_ref_raw,
            'other_compilations' => $this->other_compilations,
            'setlist' => $this->setlist,
            'special_setlist' => $this->special_setlist,
            'debut_date' => optional($this->debut_date)?->toDateString(),
            'debut_at' => $this->debut_at,
            'released' => (bool) $this->released,
            'preview_url' => $this->preview_url,
            'mv_title' => $this->mv_title,
        ];
    }
}
