<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Single;
use Illuminate\Http\Request;

class SingleController extends Controller
{
    public function index()
    {
        $singles = Single::withCount([
            'members',
            'members as center_count' => fn ($q) => $q->where('member_singles.role', 'center'),
        ])
            ->orderBy('sequence')
            ->paginate(20);

        return view('admin.singles.index', compact('singles'));
    }

    public function create()
    {
        $single = new Single;

        return view('admin.singles.create', compact('single'));
    }

    public function store(Request $request)
    {
        $data = $this->validateSingle($request);
        $single = Single::create($data);

        return redirect()
            ->route('admin.singles.index')
            ->with('success', "Single '{$single->title}' berhasil ditambahkan.");
    }

    public function edit(Single $single)
    {
        return view('admin.singles.edit', compact('single'));
    }

    public function update(Request $request, Single $single)
    {
        $data = $this->validateSingle($request);
        $single->update($data);

        return redirect()
            ->route('admin.singles.index')
            ->with('success', "Single '{$single->title}' berhasil diupdate.");
    }

    public function destroy(Single $single)
    {
        $title = $single->title;
        $single->delete();

        return redirect()
            ->route('admin.singles.index')
            ->with('success', "Single '{$title}' berhasil dihapus.");
    }

    private function validateSingle(Request $request): array
    {
        return $request->validate([
            'code' => ['required', 'string', 'max:20'],
            'title' => ['required', 'string', 'max:255'],
            'title_jp' => ['nullable', 'string', 'max:255'],
            'origin_group' => ['nullable', 'string', 'max:32'],
            'release_date' => ['nullable', 'date'],
            'release_year' => ['nullable', 'integer', 'min:2000', 'max:2100'],
            'sequence' => ['required', 'integer', 'min:1'],
            'notes' => ['nullable', 'string'],
            'mv_title' => ['nullable', 'string', 'max:255'],
            'mv_url' => ['nullable', 'string', 'max:2048'],
            'cover_art_url' => ['nullable', 'string', 'max:2048'],
            'audio_file' => ['nullable', 'string', 'max:255'],
        ]);
    }
}
