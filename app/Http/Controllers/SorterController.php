<?php

namespace App\Http\Controllers;

use App\Models\Generation;
use App\Models\Member;
use App\Models\Song;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SorterController extends Controller
{
    private const SUPPORTED_TYPES = ['member', 'song'];

    public function index()
    {
        return view('sorter.index');
    }

    public function show(string $type)
    {
        if (! in_array($type, self::SUPPORTED_TYPES, true)) {
            throw new NotFoundHttpException("Sorter type '{$type}' not supported yet.");
        }

        $method = 'data'.ucfirst($type);
        $payload = $this->{$method}();

        return view("sorter.{$type}", $payload);
    }

    /**
     * @SuppressWarnings("PHPMD.UnusedPrivateMethod") Called via dynamic dispatch in show().
     */
    private function dataMember(): array
    {
        $generations = Generation::orderByRaw("
            CASE
                WHEN code REGEXP '^[0-9]+$' THEN CAST(code AS UNSIGNED)
                ELSE 999
            END
        ")->get(['id', 'code', 'name']);

        $members = Member::with('generation:id,code,name')
            ->orderBy('name')
            ->get(['id', 'name', 'nickname', 'photo_url', 'status', 'generation_id']);

        $items = $members->map(fn ($m) => [
            'id' => $m->id,
            'name' => $m->nickname ?: $m->name,
            'full_name' => $m->name,
            'photo' => $m->photo_url ?: null,
            'status' => $m->status,
            'generation' => $m->generation ? [
                'id' => $m->generation->id,
                'code' => $m->generation->code,
                'name' => $m->generation->name,
            ] : null,
        ])->values();

        return [
            'sorterTitle' => 'JKT48 Member Sorter',
            'sorterSubtitle' => 'Urutkan member JKT48 favoritmu lewat perbandingan berpasangan.',
            'items' => $items,
            'generations' => $generations,
        ];
    }

    /**
     * @SuppressWarnings("PHPMD.UnusedPrivateMethod") Called via dynamic dispatch in show().
     */
    private function dataSong(): array
    {
        $songs = Song::with('single:id,title,cover_art_url')
            ->orderBy('title')
            ->get(['id', 'title', 'origin_group', 'single_id', 'released']);

        $originGroups = Song::query()
            ->whereNotNull('origin_group')
            ->where('origin_group', '!=', '')
            ->distinct()
            ->orderBy('origin_group')
            ->pluck('origin_group')
            ->values();

        $items = $songs->map(fn ($s) => [
            'id' => $s->id,
            'name' => $s->title,
            'full_name' => $s->title,
            'photo' => $s->single?->cover_art_url ?: null,
            'origin_group' => $s->origin_group,
            'released' => (bool) $s->released,
            'single' => $s->single ? [
                'id' => $s->single->id,
                'title' => $s->single->title,
            ] : null,
        ])->values();

        return [
            'sorterTitle' => 'JKT48 Song Sorter',
            'sorterSubtitle' => 'Urutkan lagu-lagu JKT48 favoritmu lewat perbandingan berpasangan.',
            'items' => $items,
            'originGroups' => $originGroups,
        ];
    }
}
