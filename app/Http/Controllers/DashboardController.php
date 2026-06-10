<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\DeletedCandidate;
use App\Models\District;
use App\Models\ElectionArchive;
use App\Models\ElectionSetting;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $districtFilter = request()->query('district');
        $showAllDistricts = $districtFilter === 'all';
        $selectedDistrictIds = collect(request()->query('districts', []))
            ->filter(fn ($districtId) => is_scalar($districtId) && is_numeric($districtId))
            ->map(fn ($districtId) => (int) $districtId)
            ->unique()
            ->values();

        if ($selectedDistrictIds->isEmpty() && is_numeric($districtFilter)) {
            $selectedDistrictIds = collect([(int) $districtFilter]);
        }

        $districts = District::query()
            ->with(['candidates.votes', 'votes', 'electionSetting'])
            ->orderBy('name')
            ->get();

        $charts = $districts->map(function (District $district) {
            $setting = ElectionSetting::query()->firstOrCreate(
                ['district_id' => $district->id],
                [
                    'is_active' => false,
                    'started_at' => null,
                    'ends_at' => null,
                ]
            );

            $setting = $this->syncElectionStatus($setting);
            $positions = $this->buildPositionResults($district->candidates);

            return [
                'district_id' => $district->id,
                'district' => $district->name,
                'election_title' => $setting->election_title,
                'total_votes' => $district->votes->count(),
                'positions' => $positions->values(),
                'is_active' => $setting->is_active,
                'is_paused' => $setting->isPaused(),
                'has_ended' => $setting->ended_at !== null,
                'status_label' => $setting->is_active ? 'Running' : ($setting->isPaused() ? 'Paused' : ($setting->ended_at ? 'Ended' : 'Not started')),
                'status_class' => $setting->is_active ? 'text-success' : ($setting->isPaused() ? 'text-warning' : ($setting->ended_at ? 'text-danger' : 'text-secondary')),
                'started_at' => optional($setting->started_at)?->format('M d, Y h:i A'),
                'ends_at' => optional($setting->ends_at)?->format('M d, Y h:i A'),
                'ends_at_input' => optional($setting->ends_at)?->format('Y-m-d\TH:i'),
                'countdown_target' => $setting->is_active ? optional($setting->ends_at)?->toIso8601String() : null,
                'remaining_seconds' => $setting->remaining_seconds,
                'winner_summary' => $positions
                    ->map(fn (array $position) => [
                        'position' => $position['position'],
                        'winner' => $position['winner']['name'] ?? null,
                        'winner_votes' => $position['winner']['votes'] ?? 0,
                        'is_tie' => $position['is_tie'],
                        'tie_votes' => $position['tie_votes'],
                        'tied_candidates' => $position['tied_candidates'],
                    ])
                    ->values(),
            ];
        })->values();

        $filterDistricts = $charts->values();
        $visibleCharts = $showAllDistricts
            ? $filterDistricts
            : ($selectedDistrictIds->isNotEmpty()
                ? $filterDistricts->whereIn('district_id', $selectedDistrictIds)->values()
                : collect());
        $presidentVoteDetails = $charts
            ->map(function (array $chart) {
                $presidentVotes = collect($chart['positions'])
                    ->firstWhere('position', 'President')['total_votes'] ?? 0;

                return [
                    'district' => $chart['district'],
                    'votes' => $presidentVotes,
                ];
            })
            ->filter(fn (array $detail) => $detail['votes'] > 0)
            ->values();
        $vicePresidentVoteDetails = $charts
            ->map(function (array $chart) {
                $vicePresidentVotes = collect($chart['positions'])
                    ->firstWhere('position', 'Vice President')['total_votes'] ?? 0;

                return [
                    'district' => $chart['district'],
                    'votes' => $vicePresidentVotes,
                ];
            })
            ->filter(fn (array $detail) => $detail['votes'] > 0)
            ->values();
        $totalVoteDetails = $charts
            ->map(fn (array $chart) => [
                'district' => $chart['district'],
                'votes' => $chart['total_votes'],
            ])
            ->filter(fn (array $detail) => $detail['votes'] > 0)
            ->values();
        $runningDistrictDetails = $charts
            ->filter(fn (array $chart) => $chart['is_active'])
            ->map(fn (array $chart) => [
                'district' => $chart['district'],
                'status' => 'Running',
            ])
            ->values();

        return view('dashboard.index', [
            'charts' => $charts,
            'visibleCharts' => $visibleCharts,
            'filterDistricts' => $filterDistricts,
            'selectedDistrictIds' => $selectedDistrictIds->all(),
            'showAllDistricts' => $showAllDistricts,
            'singleDistrictMode' => $charts->count() === 1,
            'totalVotes' => Vote::count(),
            'presidentVotes' => Vote::query()->where('position', 'President')->count(),
            'vicePresidentVotes' => Vote::query()->where('position', 'Vice President')->count(),
            'totalDistricts' => $charts->count(),
            'runningDistricts' => $charts->where('is_active', true)->count(),
            'runningDistrictDetails' => $runningDistrictDetails,
            'totalVoteDetails' => $totalVoteDetails,
            'presidentVoteDetails' => $presidentVoteDetails,
            'vicePresidentVoteDetails' => $vicePresidentVoteDetails,
            'electionArchives' => ElectionArchive::query()->whereNull('restored_at')->latest('deleted_at')->latest('id')->get(),
            'deletedCandidates' => DeletedCandidate::query()->whereNull('restored_at')->latest('deleted_at')->latest('id')->get(),
        ]);
    }

    public function startElection(District $district): RedirectResponse
    {
        $validated = request()->validate([
            'election_title' => ['required', 'string', 'max:120', 'regex:/^[\pL\pN ]+$/u'],
            'ends_at' => ['required', 'date', 'after:now'],
        ], [
            'election_title.required' => 'Please enter the election title.',
            'election_title.regex' => 'Election title can contain only letters, numbers, and spaces.',
            'ends_at.required' => 'Please choose the election end time.',
            'ends_at.after' => 'Election end time must be in the future.',
        ]);

        DB::transaction(function () use ($district, $validated) {
            $district->loadMissing(['candidates.votes', 'votes', 'electionSetting', 'users']);

            $setting = ElectionSetting::query()->firstOrCreate(
                ['district_id' => $district->id],
                ['is_active' => false]
            );

            $setting = $this->syncElectionStatus($setting);

            if (
                $setting->ended_at &&
                ($district->votes->isNotEmpty() || $district->users->contains(fn (User $user) => $user->has_voted_at !== null))
            ) {
                $this->archiveElection($district, $setting, 'restarted');
                $this->resetElectionData($district);
            }

            $setting->update([
                'election_title' => trim($validated['election_title']),
                'is_active' => true,
                'started_at' => $setting->is_active && $setting->started_at ? $setting->started_at : now(),
                'paused_at' => null,
                'remaining_seconds' => null,
                'ended_at' => null,
                'ends_at' => Carbon::parse($validated['ends_at']),
            ]);
        });

        return back()->with('status', "Election started successfully for {$district->name}.");
    }

    public function pauseElection(District $district): RedirectResponse
    {
        $setting = ElectionSetting::query()->firstOrCreate(['district_id' => $district->id], ['is_active' => false]);
        $setting = $this->syncElectionStatus($setting);

        if (! $setting->is_active || ! $setting->ends_at) {
            return back()->withErrors(['pause' => 'Only a running election can be paused.']);
        }

        $remainingSeconds = max(now()->diffInSeconds($setting->ends_at, false), 0);

        $setting->update([
            'is_active' => false,
            'paused_at' => now(),
            'remaining_seconds' => $remainingSeconds,
        ]);

        return back()->with('status', "Election paused for {$district->name}.");
    }

    public function resumeElection(District $district): RedirectResponse
    {
        $setting = ElectionSetting::query()->firstOrCreate(['district_id' => $district->id], ['is_active' => false]);
        $setting = $this->syncElectionStatus($setting);

        if (! $setting->isPaused() || ! $setting->remaining_seconds) {
            return back()->withErrors(['resume' => 'This election is not paused.']);
        }

        $setting->update([
            'is_active' => true,
            'paused_at' => null,
            'ends_at' => now()->addSeconds($setting->remaining_seconds),
            'remaining_seconds' => null,
        ]);

        return back()->with('status', "Election resumed for {$district->name}.");
    }

    public function destroyElection(District $district): RedirectResponse
    {
        DB::transaction(function () use ($district) {
            $district->loadMissing(['candidates.votes', 'votes', 'electionSetting', 'users']);
            $setting = ElectionSetting::query()->firstOrCreate(['district_id' => $district->id], ['is_active' => false]);
            $setting = $this->syncElectionStatus($setting);

            $this->archiveElection($district, $setting, 'deleted');

            User::query()
                ->where('district_id', $district->id)
                ->update([
                    'last_known_district_name' => $district->name,
                    'district_id' => null,
                    'has_voted_at' => null,
                ]);

            $district->delete();
        });

        return back()->with('status', 'Election card deleted and archived successfully.');
    }

    public function restoreElection(ElectionArchive $archive): RedirectResponse
    {
        if (District::query()->where('name', $archive->district_name)->exists()) {
            return back()->withErrors([
                'restore' => "A district named {$archive->district_name} already exists. Delete or rename it before restoring this archive.",
            ]);
        }

        DB::transaction(function () use ($archive) {
            $district = District::query()->create([
                'name' => $archive->district_name,
                'is_active' => true,
            ]);

            User::query()
                ->whereNull('district_id')
                ->where('last_known_district_name', $archive->district_name)
                ->update([
                    'district_id' => $district->id,
                ]);

            ElectionSetting::query()->create([
                'district_id' => $district->id,
                'election_title' => $archive->election_title,
                'is_active' => false,
                'started_at' => null,
                'ends_at' => null,
                'ended_at' => null,
            ]);

            $archive->loadMissing('deletedCandidates');

            foreach ($archive->deletedCandidates as $deletedCandidate) {
                Candidate::query()->create([
                    'district_id' => $district->id,
                    'name' => $deletedCandidate->candidate_name,
                    'age' => $deletedCandidate->age ?? 0,
                    'position' => $deletedCandidate->position ?: 'President',
                    'image_path' => $deletedCandidate->image_path,
                    'vision_path' => $deletedCandidate->vision_path,
                    'email' => $this->resolveRestoredCandidateEmail($deletedCandidate->email, $deletedCandidate->candidate_name),
                    'is_active' => true,
                ]);
            }

            $archive->deletedCandidates()->update([
                'restored_at' => now(),
            ]);

            $archive->update([
                'restored_at' => now(),
            ]);
        });

        return back()->with('status', "Election card {$archive->district_name} restored successfully. Vote count will start from 0.");
    }

    public function destroyDeletedCandidate(DeletedCandidate $deletedCandidate): RedirectResponse
    {
        $deletedCandidate->delete();

        return back()->with('status', 'Candidate history permanently deleted.');
    }

    private function syncElectionStatus(ElectionSetting $setting): ElectionSetting
    {
        if (! $setting->started_at) {
            if ($setting->is_active || $setting->ended_at) {
                $setting->update([
                    'is_active' => false,
                    'paused_at' => null,
                    'remaining_seconds' => null,
                    'ended_at' => null,
                ]);
            }

            return $setting->fresh();
        }

        if ($setting->is_active && $setting->hasEnded()) {
            $setting->update([
                'is_active' => false,
                'paused_at' => null,
                'remaining_seconds' => null,
                'ended_at' => $setting->ended_at ?: now(),
            ]);
        }

        return $setting->fresh();
    }

    private function buildPositionResults(Collection $candidates): Collection
    {
        return $candidates
            ->groupBy(fn (Candidate $candidate) => trim($candidate->position ?: 'Other'))
            ->map(function (Collection $positionCandidates, string $position) {
                $labels = $positionCandidates->pluck('name')->values();
                $voteTotals = $positionCandidates->map(fn (Candidate $candidate) => $candidate->votes->count())->values();
                $leaderIndex = $voteTotals->search($voteTotals->max());
                $maxVotes = $voteTotals->max();
                $tiedCandidates = $maxVotes > 0
                    ? $positionCandidates
                        ->filter(fn (Candidate $candidate) => $candidate->votes->count() === $maxVotes)
                        ->sortBy('name')
                        ->values()
                    : collect();
                $isTie = $tiedCandidates->count() > 1;
                $winner = $isTie ? null : $this->resolveWinner($positionCandidates);
                $tieNames = $tiedCandidates->pluck('name')->values()->all();

                return [
                    'position' => $position,
                    'labels' => $labels,
                    'votes' => $voteTotals,
                    'leader' => $isTie
                        ? 'Draw between '.implode(', ', $tieNames)
                        : ($labels[$leaderIndex] ?? 'No leader yet'),
                    'total_votes' => $voteTotals->sum(),
                    'is_tie' => $isTie,
                    'tie_votes' => $isTie ? $maxVotes : 0,
                    'tied_candidates' => $tieNames,
                    'winner' => $winner ? [
                        'name' => $winner->name,
                        'votes' => $winner->votes->count(),
                        'image_path' => $winner->image_path,
                        'vision_path' => $winner->vision_path,
                    ] : null,
                ];
            })
            ->sortBy('position')
            ->values();
    }

    private function resolveWinner(Collection $candidates): ?Candidate
    {
        $winner = $candidates->reduce(function (?Candidate $currentWinner, Candidate $candidate) {
            if (! $currentWinner) {
                return $candidate;
            }

            $currentVotes = $currentWinner->votes->count();
            $candidateVotes = $candidate->votes->count();

            if ($candidateVotes > $currentVotes) {
                return $candidate;
            }

            if ($candidateVotes === $currentVotes && strcmp($candidate->name, $currentWinner->name) < 0) {
                return $candidate;
            }

            return $currentWinner;
        });

        if (! $winner || $winner->votes->count() === 0) {
            return null;
        }

        return $winner;
    }

    private function archiveElection(District $district, ElectionSetting $setting, string $reason): ElectionArchive
    {
        $district->loadMissing(['candidates.votes', 'votes']);
        $positionResults = $this->buildPositionResults($district->candidates);

        $archive = ElectionArchive::query()->create([
            'district_name' => $district->name,
            'election_title' => $setting->election_title,
            'archive_reason' => $reason,
            'candidate_count' => $district->candidates->count(),
            'total_votes' => $district->votes->count(),
            'election_started_at' => $setting->started_at,
            'election_ended_at' => $setting->ended_at ?: $setting->ends_at ?: now(),
            'deleted_at' => now(),
            'winners' => $positionResults
                ->map(fn (array $position) => [
                    'position' => $position['position'],
                    'winner' => $position['winner'],
                    'is_tie' => $position['is_tie'],
                    'tie_votes' => $position['tie_votes'],
                    'tied_candidates' => $position['tied_candidates'],
                ])
                ->values()
                ->all(),
            'position_results' => $positionResults->values()->all(),
        ]);

        foreach ($district->candidates as $candidate) {
            DeletedCandidate::query()->create([
                'election_archive_id' => $archive->id,
                'original_candidate_id' => $candidate->id,
                'district_name' => $district->name,
                'candidate_name' => $candidate->name,
                'age' => $candidate->age,
                'position' => $candidate->position,
                'email' => $candidate->email,
                'image_path' => $candidate->image_path,
                'vision_path' => $candidate->vision_path,
                'vote_count' => $candidate->votes->count(),
                'deleted_reason' => $reason === 'deleted' ? 'election_deleted' : 'election_restarted',
                'election_started_at' => $setting->started_at,
                'election_ended_at' => $setting->ended_at ?: $setting->ends_at ?: now(),
                'deleted_at' => now(),
            ]);
        }

        return $archive;
    }

    private function resetElectionData(District $district): void
    {
        Vote::query()->where('district_id', $district->id)->delete();

        User::query()
            ->where('district_id', $district->id)
            ->update(['has_voted_at' => null]);
    }

    private function resolveRestoredCandidateEmail(?string $email, string $candidateName): string
    {
        $baseEmail = $email ?: strtolower(str_replace(' ', '.', trim($candidateName))).'@restored.local';

        if (! Candidate::query()->where('email', $baseEmail)->exists()) {
            return $baseEmail;
        }

        $parts = explode('@', $baseEmail, 2);
        $local = $parts[0] ?: 'candidate';
        $domain = $parts[1] ?? 'restored.local';

        return $local.'.restored.'.now()->format('YmdHis').'@'.$domain;
    }
}
