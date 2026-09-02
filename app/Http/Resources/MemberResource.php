<?php

namespace App\Http\Resources;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Member
 */
class MemberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                        => $this->id,
            'name'                      => $this->name,
            'nickname'                  => $this->nickname,
            'birth_place'               => $this->birth_place,
            'birth_date'                => optional($this->birth_date)?->toDateString(),
            'status'                    => $this->status,
            'restructure_status'        => $this->restructure_status,
            'photo_url'                 => $this->photo_url,
            'bio'                       => $this->bio,
            'join_date'                 => optional($this->join_date)?->toDateString(),
            'effective_join_date'       => optional($this->effective_join_date)?->toDateString(),
            'cancelled_date'            => optional($this->cancelled_date)?->toDateString(),
            'rejoin_date'               => optional($this->rejoin_date)?->toDateString(),
            'promotion_date'            => optional($this->promotion_date)?->toDateString(),
            'graduation_announce_date'  => optional($this->graduation_announce_date)?->toDateString(),
            'graduation_announce_event' => $this->graduation_announce_event,
            'graduation_date'           => optional($this->graduation_date)?->toDateString(),
            'current_age'               => $this->current_age,
            'age_at_join'               => $this->age_at_join,
            'age_at_graduation'         => $this->age_at_graduation,
            'days_in_jkt48'             => $this->days_in_jkt48,
            'generation'                => $this->whenLoaded('generation', fn () => [
                'id'   => $this->generation->id,
                'code' => $this->generation->code,
                'name' => $this->generation->name,
            ]),
            'current_teams' => $this->whenLoaded('currentTeams', fn () => $this->currentTeams
                ->map(fn ($mt) => [
                    'team_id'     => $mt->team_id,
                    'joined_date' => optional($mt->joined_date)?->toDateString(),
                    'team'        => $mt->relationLoaded('team') && $mt->team
                        ? ['id' => $mt->team->id, 'code' => $mt->team->code, 'name' => $mt->team->name]
                        : null,
                ])
            ),
            'singles' => $this->whenLoaded('singles', fn () => $this->singles
                ->map(fn ($s) => [
                    'id'           => $s->id,
                    'code'         => $s->code,
                    'title'        => $s->title,
                    'release_date' => optional($s->release_date)?->toDateString(),
                    'role'         => $s->pivot->role ?? null,
                    'position'     => $s->pivot->position ?? null,
                ])
            ),
        ];
    }
}
