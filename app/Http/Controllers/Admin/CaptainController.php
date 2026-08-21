<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Captain;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CaptainController extends Controller
{
    public function index()
    {
        $captains = Captain::with('member.generation')
            ->orderByDesc('start_date')
            ->paginate(20);

        return view('admin.captains.index', compact('captains'));
    }

    public function create()
    {
        $members = Member::orderBy('name')->get();
        $captain = new Captain;
        return view('admin.captains.create', compact('members', 'captain'));
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
        return view('admin.captains.edit', compact('captain', 'members'));
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
            'position' => ['required', Rule::in([
                'Kapten JKT48',
                'Wakil Kapten JKT48',
                'Kapten Tim J',
                'Kapten Tim KIII',
                'Kapten Tim T',
            ])],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
