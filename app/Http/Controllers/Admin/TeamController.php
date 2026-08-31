<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TeamController extends Controller
{
    public function index()
    {
        $teams = Team::withCount(['memberships', 'captains'])
            ->orderBy('code')
            ->get();

        return view('admin.teams.index', compact('teams'));
    }

    public function create()
    {
        $team = new Team;

        return view('admin.teams.create', compact('team'));
    }

    public function store(Request $request)
    {
        $data = $this->validateTeam($request);
        Team::create($data);

        return redirect()
            ->route('admin.teams.index')
            ->with('success', 'Tim berhasil ditambahkan.');
    }

    public function edit(Team $team)
    {
        return view('admin.teams.edit', compact('team'));
    }

    public function update(Request $request, Team $team)
    {
        $data = $this->validateTeam($request, $team->id);
        $team->update($data);

        return redirect()
            ->route('admin.teams.index')
            ->with('success', 'Tim berhasil diupdate.');
    }

    public function destroy(Team $team)
    {
        $team->delete();

        return redirect()
            ->route('admin.teams.index')
            ->with('success', 'Tim berhasil dihapus.');
    }

    private function validateTeam(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'code' => ['required', 'string', 'max:20', Rule::unique('teams', 'code')->ignore($ignoreId)],
            'name' => ['required', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:20'],
            'formed_at' => ['nullable', 'date'],
            'disbanded_at' => ['nullable', 'date', 'after_or_equal:formed_at'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
