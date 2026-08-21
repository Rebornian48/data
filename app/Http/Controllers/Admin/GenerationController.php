<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Generation;
use Illuminate\Http\Request;

class GenerationController extends Controller
{
    public function index()
    {
        $generations = Generation::withCount([
            'members',
            'members as active_count' => fn ($q) => $q->where('status', 'Aktif'),
        ])
        ->orderBy('id')
        ->get();

        return view('admin.generations.index', compact('generations'));
    }

    public function create()
    {
        $generation = new Generation;
        return view('admin.generations.create', compact('generation'));
    }

    public function store(Request $request)
    {
        $data = $this->validateGeneration($request);
        $gen = Generation::create($data);

        return redirect()
            ->route('admin.generations.index')
            ->with('success', "Generasi '{$gen->name}' berhasil ditambahkan.");
    }

    public function edit(Generation $generation)
    {
        return view('admin.generations.edit', compact('generation'));
    }

    public function update(Request $request, Generation $generation)
    {
        $data = $this->validateGeneration($request);
        $generation->update($data);

        return redirect()
            ->route('admin.generations.index')
            ->with('success', "Generasi '{$generation->name}' berhasil diupdate.");
    }

    public function destroy(Generation $generation)
    {
        if ($generation->members()->exists()) {
            return back()->with('error', 'Tidak dapat menghapus generasi yang masih memiliki member.');
        }

        $name = $generation->name;
        $generation->delete();

        return redirect()
            ->route('admin.generations.index')
            ->with('success', "Generasi '{$name}' berhasil dihapus.");
    }

    private function validateGeneration(Request $request): array
    {
        return $request->validate([
            'code' => ['required', 'string', 'max:20'],
            'name' => ['required', 'string', 'max:100'],
            'announcement_date' => ['nullable', 'date'],
            'join_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string'],
        ]);
    }
}
