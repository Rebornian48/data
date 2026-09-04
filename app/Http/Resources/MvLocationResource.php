<?php

namespace App\Http\Resources;

use App\Models\MvLocation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin MvLocation
 */
class MvLocationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category' => $this->category,
            'song_title' => $this->song_title,
            'song_title_jp' => $this->song_title_jp,
            'release_year' => $this->release_year,
            'location' => $this->location,
            'position' => $this->position,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'notes' => $this->notes,
        ];
    }
}
