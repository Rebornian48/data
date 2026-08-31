<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Generation;
use App\Models\Member;
use App\Models\MemberTeam;
use App\Models\Single;
use App\Models\Team;
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
        $teams = Team::orderBy('code')->get();
        $member = new Member;

        return view('admin.members.create', compact('generations', 'singles', 'teams', 'member'));
    }

    public function store(Request $request)
    {
        $data = $this->validateMember($request);
        $singles = $request->input('singles', []);
        $teamHistory = $this->validateTeamHistory($request);

        $member = Member::create($data);
        $this->syncSingles($member, $singles);
        $this->syncTeamHistory($member, $teamHistory);

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
        $teams = Team::orderBy('code')->get();
        $member->load(['singles', 'teamHistory.team']);

        return view('admin.members.edit', compact('member', 'generations', 'singles', 'teams'));
    }

    public function update(Request $request, Member $member)
    {
        $data = $this->validateMember($request);
        $singles = $request->input('singles', []);
        $teamHistory = $this->validateTeamHistory($request);

        $member->update($data);
        $this->syncSingles($member, $singles);
        $this->syncTeamHistory($member, $teamHistory);

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

    private function validateMember(Request $request): array
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
            'photo_url' => ['nullable', 'string', 'max:500'],
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
            if (! in_array($role, ['member', 'center'])) {
                continue;
            }
            $sync[$singleId] = ['role' => $role];
        }
        $member->singles()->sync($sync);
    }

    private function validateTeamHistory(Request $request): array
    {
        $rows = $request->input('team_history', []);
        $clean = [];
        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }
            $teamId = $row['team_id'] ?? null;
            $joined = $row['joined_date'] ?? null;
            if (! $teamId || ! $joined) {
                continue;
            }
            $clean[] = [
                'team_id' => (int) $teamId,
                'joined_date' => $joined,
                'left_date' => $row['left_date'] ?: null,
                'notes' => $row['notes'] ?? null,
            ];
        }

        $request->merge(['team_history_clean' => $clean]);
        $request->validate([
            'team_history_clean.*.team_id' => ['required', 'exists:teams,id'],
            'team_history_clean.*.joined_date' => ['required', 'date'],
            'team_history_clean.*.left_date' => ['nullable', 'date'],
        ]);

        return $clean;
    }

    private function syncTeamHistory(Member $member, array $rows): void
    {
        $member->teamHistory()->delete();
        foreach ($rows as $r) {
            MemberTeam::create([
                'member_id' => $member->id,
                'team_id' => $r['team_id'],
                'joined_date' => $r['joined_date'],
                'left_date' => $r['left_date'],
                'notes' => $r['notes'],
            ]);
        }
    }
}
