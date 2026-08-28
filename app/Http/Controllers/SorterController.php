<?php

namespace App\Http\Controllers;

use App\Models\Generation;
use App\Models\Member;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SorterController extends Controller
{
    private const SUPPORTED_TYPES = ['member'];

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
}
