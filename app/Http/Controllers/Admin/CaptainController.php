<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Captain;
use App\Models\Member;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CaptainController extends Controller
{
    public function index()
    {
        $captains = Captain::with(['member.generation', 'team'])
            ->orderByDesc('start_date')
            ->paginate(20);

        return view('admin.captains.index', compact('captains'));
    }

    public function create()
    {
        $members = Member::orderBy('name')->get();
        $teams = Team::orderBy('code')->get();
        $captain = new Captain;

        return view('admin.captains.create', compact('members', 'teams', 'captain'));
    }

    public function store(Request $request)
    {
        $data = $this->validateCaptain($request);
        Captain::create($data);

        return redirect()
            ->route('admin.captains.index')
            ->with('success', 'Data kapten berhasil ditambahkan.');
    }

    public function edit(Captain $captain)
    {
        $members = Member::orderBy('name')->get();
        $teams = Team::orderBy('code')->get();

        return view('admin.captains.edit', compact('captain', 'members', 'teams'));
    }

    public function update(Request $request, Captain $captain)
    {
        $data = $this->validateCaptain($request);
        $captain->update($data);

        return redirect()
            ->route('admin.captains.index')
            ->with('success', 'Data kapten berhasil diupdate.');
    }

    public function destroy(Captain $captain)
    {
        $captain->delete();

        return redirect()
            ->route('admin.captains.index')
            ->with('success', 'Data kapten berhasil dihapus.');
    }

    private function validateCaptain(Request $request): array
    {
        return $request->validate([
            'member_id' => ['required', 'exists:members,id'],
            'team_id' => ['nullable', 'exists:teams,id'],
            'role' => ['required', Rule::in(['Kapten', 'Wakil Kapten'])],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
