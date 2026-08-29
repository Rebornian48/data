<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Map;
use App\Models\MapSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MapController extends Controller
{
    public function index()
    {
        $maps = Map::withCount(['points', 'polylines', 'polygonLayers', 'notes'])
            ->orderBy('id')
            ->get();

        return view('admin.maps.index', compact('maps'));
    }

    public function create()
    {
        $map = new Map;

        return view('admin.maps.create', ['map' => $map, 'settings' => []]);
    }

    public function store(Request $request)
    {
        $data = $this->validateMap($request);
        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);

        $map = Map::create($data);
        $this->syncSettings($map, $request->input('settings', []));

        return redirect()
            ->route('admin.maps.index')
            ->with('success', "Peta '{$map->title}' berhasil ditambahkan.");
    }

    public function edit(Map $map)
    {
        $settings = $map->settings()->orderBy('key')->get(['id', 'key', 'value'])->toArray();

        return view('admin.maps.edit', compact('map', 'settings'));
    }

    public function update(Request $request, Map $map)
    {
        $data = $this->validateMap($request, $map->id);
        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);

        $map->update($data);
        $this->syncSettings($map, $request->input('settings', []));

        return redirect()
            ->route('admin.maps.index')
            ->with('success', "Peta '{$map->title}' berhasil diupdate.");
    }

    public function destroy(Map $map)
    {
        $title = $map->title;
        $map->delete();

        return redirect()
            ->route('admin.maps.index')
            ->with('success', "Peta '{$title}' berhasil dihapus.");
    }

    private function validateMap(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'slug'            => ['nullable', 'string', 'max:64', 'unique:maps,slug' . ($ignoreId ? ",{$ignoreId}" : '')],
            'title'           => ['required', 'string', 'max:255'],
            'subtitle'        => ['nullable', 'string', 'max:255'],
            'google_sheet_id' => ['nullable', 'string', 'max:64'],
            'is_published'    => ['nullable', 'boolean'],
        ]) + ['is_published' => (bool) $request->boolean('is_published')];
    }

    private function syncSettings(Map $map, array $rows): void
    {
        $seen = [];

        foreach ($rows as $row) {
            $key = trim((string) ($row['key'] ?? ''));
            if ($key === '') continue;

            MapSetting::updateOrCreate(
                ['map_id' => $map->id, 'key' => $key],
                ['value'  => $row['value'] ?? null]
            );
            $seen[] = $key;
        }

        $map->settings()->whereNotIn('key', $seen)->delete();
    }
}
