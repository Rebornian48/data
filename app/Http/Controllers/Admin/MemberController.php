<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Generation;
use App\Models\Member;
use App\Models\Single;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $members = Member::with('generation')
            ->search($request->input('q'))
            ->when($request->input('generation'), fn ($q, $v) => $q->where('generation_id', $v))
            ->when($request->input('status'), fn ($q, $v) => $q->where('status', $v))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $generations = Generation::orderBy('id')->get();

        return view('admin.members.index', compact('members', 'generations'));
    }

    public function create()
    {
        $generations = Generation::orderBy('id')->get();
        $singles = Single::orderBy('sequence')->get();
        return view('admin.members.create', compact('generations', 'singles'));
    }

    public function store(Request $request)
    {
        $data = $this->validateMember($request);
        $singles = $request->input('singles', []);

        $member = Member::create($data);
        $this->syncSingles($member, $singles);

        return redirect()
            ->route('admin.members.index')
            ->with('success', "Member '{$member->name}' berhasil ditambahkan.");
    }

    public function show(Member $member)
    {
        $member->load(['generation', 'singles', 'captains']);
        return view('admin.members.show', compact('member'));
    }

    public function edit(Member $member)
    {
        $generations = Generation::orderBy('id')->get();
        $singles = Single::orderBy('sequence')->get();
        $member->load('singles');
        return view('admin.members.edit', compact('member', 'generations', 'singles'));
    }

    public function update(Request $request, Member $member)
    {
        $data = $this->validateMember($request, $member);
        $singles = $request->input('singles', []);

        $member->update($data);
        $this->syncSingles($member, $singles);

        return redirect()
            ->route('admin.members.index')
            ->with('success', "Member '{$member->name}' berhasil diupdate.");
    }

    public function destroy(Member $member)
    {
        $name = $member->name;
        $member->delete();

        return redirect()
            ->route('admin.members.index')
            ->with('success', "Member '{$name}' berhasil dihapus.");
    }

    private function validateMember(Request $request, ?Member $member = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nickname' => ['nullable', 'string', 'max:100'],
            'birth_place' => ['nullable', 'string', 'max:150'],
            'birth_date' => ['nullable', 'date'],
            'generation_id' => ['required', 'exists:generations,id'],
            'join_date' => ['nullable', 'date'],
            'cancelled_date' => ['nullable', 'date'],
            'rejoin_date' => ['nullable', 'date'],
            'promotion_date' => ['nullable', 'date'],
            'graduation_announce_date' => ['nullable', 'date'],
            'graduation_announce_event' => ['nullable', 'string', 'max:255'],
            'graduation_date' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['Aktif', 'Lulus'])],
            'restructure_status' => ['nullable', 'string', 'max:100'],
            'photo_url' => ['nullable', 'url', 'max:500'],
            'bio' => ['nullable', 'string'],
        ]);
    }

    /**
     * Sync single participations. $singles is an array where key = single_id
     * and value = 'member' | 'center' | null (skip).
     */
    private function syncSingles(Member $member, array $singles): void
    {
        $sync = [];
        foreach ($singles as $singleId => $role) {
            if (! in_array($role, ['member', 'center'])) continue;
            $sync[$singleId] = ['role' => $role];
        }
        $member->singles()->sync($sync);
    }
}
