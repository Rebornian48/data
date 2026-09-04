<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MvLocation;
use Illuminate\Http\Request;

class MvLocationController extends Controller
{
    public function index(Request $request)
    {
        $query = MvLocation::query();
        if ($term = $request->input('q')) {
            $query->where(fn ($q) => $q->where('song_title', 'like', "%{$term}%")
                ->orWhere('location', 'like', "%{$term}%"));
        }
        $rows = $query->orderBy('release_year')->orderBy('song_title')->paginate(50)->withQueryString();

        return view('admin.mv_locations.index', compact('rows'));
    }

    public function create()
    {
        return view('admin.mv_locations.create', ['row' => new MvLocation]);
    }

    public function store(Request $request)
    {
        $data = $this->validate_($request);
        MvLocation::create($data);

        return redirect()->route('admin.mv-locations.index')
            ->with('success', 'Lokasi MV berhasil ditambahkan.');
    }

    public function edit(MvLocation $mvLocation)
    {
        return view('admin.mv_locations.edit', ['row' => $mvLocation]);
    }

    public function update(Request $request, MvLocation $mvLocation)
    {
        $data = $this->validate_($request);
        $mvLocation->update($data);

        return redirect()->route('admin.mv-locations.index')
            ->with('success', 'Lokasi MV berhasil diupdate.');
    }

    public function destroy(MvLocation $mvLocation)
    {
        $mvLocation->delete();

        return redirect()->route('admin.mv-locations.index')
            ->with('success', 'Lokasi MV berhasil dihapus.');
    }

    private function validate_(Request $request): array
    {
        return $request->validate([
            'category' => ['nullable', 'string', 'max:100'],
            'song_title' => ['required', 'string', 'max:255'],
            'song_title_jp' => ['nullable', 'string', 'max:255'],
            'release_year' => ['nullable', 'integer', 'min:2000', 'max:2100'],
            'location' => ['required', 'string', 'max:255'],
            'position' => ['nullable', 'integer', 'min:1'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
