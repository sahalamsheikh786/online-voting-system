<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Candidate;
use App\Models\District;
use App\Models\ElectionArchive;
use App\Models\ElectionSetting;
use App\Models\User;
use App\Models\Vote;
use App\Support\Audit\AuditLogger;
use App\Support\Reports\ExcelReportExporter;
use App\Support\Reports\PdfReportExporter;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        return view('reports.index', [
            'districts' => District::query()->orderBy('name')->get(),
            'reportCards' => $this->reportCards(),
        ]);
    }

    public function export(Request $request, string $type, string $format, ExcelReportExporter $excelExporter, PdfReportExporter $pdfExporter): Response
    {
        abort_unless(array_key_exists($type, $this->reportCards()), 404);
        abort_unless(in_array($format, ['pdf', 'excel'], true), 404);

        $validated = $request->validate([
            'district' => [
                'required',
                Rule::in([
                    'all',
                    ...District::query()->pluck('id')->map(fn ($id) => (string) $id)->all(),
                ]),
            ],
        ], [
            'district.required' => 'Please select a district first.',
            'district.in' => 'Please choose a valid district filter.',
        ]);

        $selectedDistrict = $validated['district'] === 'all'
            ? null
            : District::query()->findOrFail((int) $validated['district']);

        $payload = match ($type) {
            'election-results' => $this->buildElectionResultsReport($selectedDistrict),
            'voter-list' => $this->buildVoterListReport($selectedDistrict),
            'candidate-report' => $this->buildCandidateReport($selectedDistrict),
            'audit-log' => $this->buildAuditLogReport($selectedDistrict),
            'election-summary' => $this->buildElectionSummaryReport($selectedDistrict),
        };

        AuditLogger::record(
            'report_exported',
            auth()->user(),
            "{$payload['title']} exported in ".strtoupper($format)." format.",
            $selectedDistrict?->id,
            [
                'report_type' => $type,
                'format' => $format,
                'district' => $selectedDistrict?->name ?? 'All Districts',
            ]
        );

        $filename = str($payload['title'])->slug()->append('-', now()->format('Ymd-His'))->toString();

        return $format === 'pdf'
            ? $pdfExporter->download($payload, $filename)
            : $excelExporter->download($payload, $filename);
    }

    private function buildElectionResultsReport(?District $selectedDistrict): array
    {
        $districts = $this->districtScope($selectedDistrict)
            ->load(['votes', 'users', 'candidates.votes', 'electionSetting']);

        $candidateRows = [];
        $winnerRows = [];
        $districtRows = [];
        $turnoutPercentages = [];

        foreach ($districts as $district) {
            $approvedVoters = $district->users->where('status', 'approved')->count();
            $votesCast = $district->votes->count();
            $presidentVotes = $district->votes->where('position', 'President')->count();
            $vicePresidentVotes = $district->votes->where('position', 'Vice President')->count();
            $turnout = $approvedVoters > 0 ? round(($votesCast / max($approvedVoters * 2, 1)) * 100, 2) : 0;
            $turnoutPercentages[] = $turnout;
            $electionTitle = $this->electionTitleForDistrict($district);

            $districtRows[] = [
                $district->name,
                $electionTitle,
                $votesCast,
                $presidentVotes,
                $vicePresidentVotes,
                $approvedVoters,
                number_format($turnout, 2).'%',
                $district->electionSetting?->is_active ? 'Active' : ($district->electionSetting?->hasEnded() ? 'Completed' : 'Pending'),
            ];

            foreach ($district->candidates->sortBy('name') as $candidate) {
                $candidateRows[] = [
                    $district->name,
                    $electionTitle,
                    $candidate->name,
                    $candidate->party ?: 'Independent',
                    $candidate->position,
                    $candidate->votes->count(),
                ];
            }

            foreach ($district->candidates->groupBy('position') as $position => $group) {
                $ranked = $group
                    ->sortByDesc(fn (Candidate $candidate) => $candidate->votes->count())
                    ->values();

                $winner = $ranked->get(0);
                $topVotes = $winner?->votes->count() ?? 0;
                $tiedCandidates = $topVotes > 0
                    ? $ranked
                        ->filter(fn (Candidate $candidate) => $candidate->votes->count() === $topVotes)
                        ->sortBy('name')
                        ->values()
                    : collect();
                $isTie = $tiedCandidates->count() > 1;

                $winnerRows[] = [
                    $district->name,
                    $electionTitle,
                    $position ?: 'Other',
                    $isTie ? 'Draw Vote' : 'Winner',
                    $isTie ? implode(', ', $tiedCandidates->pluck('name')->all()) : ($winner?->name ?? 'No winner yet'),
                    $topVotes,
                ];
            }
        }

        return [
            'title' => 'Election Results Report',
            'context' => $this->reportContext($selectedDistrict),
            'generated_at' => now()->format('M d, Y h:i A'),
            'summary' => [
                ['label' => 'Election Title', 'value' => $this->reportElectionTitleValue($selectedDistrict)],
                ['label' => 'Tracked districts', 'value' => $districts->count()],
                ['label' => 'Total votes cast', 'value' => $districts->sum(fn (District $district) => $district->votes->count())],
                ['label' => 'President votes cast', 'value' => $districts->sum(fn (District $district) => $district->votes->where('position', 'President')->count())],
                ['label' => 'Vice President votes cast', 'value' => $districts->sum(fn (District $district) => $district->votes->where('position', 'Vice President')->count())],
                ['label' => 'Candidates in report', 'value' => count($candidateRows)],
                ['label' => 'Average turnout', 'value' => number_format(collect($turnoutPercentages)->avg() ?? 0, 2).'%'],
            ],
            'sections' => [
                [
                    'title' => 'District Wise Vote Counts',
                    'headers' => ['District', 'Election Title', 'Total Votes', 'President Votes', 'Vice President Votes', 'Approved Voters', 'Turnout', 'Election Status'],
                    'rows' => $districtRows,
                ],
                [
                    'title' => 'Candidate Wise Total Votes',
                    'headers' => ['District', 'Election Title', 'Candidate', 'Party', 'Position', 'Votes'],
                    'rows' => $candidateRows,
                ],
                [
                    'title' => 'Winner And Runner Up Summary',
                    'headers' => ['District', 'Election Title', 'Position', 'Result', 'Winner / Draw Details', 'Top Votes'],
                    'rows' => $winnerRows,
                ],
            ],
            'notes' => [
                'Turnout is calculated against two position votes per approved voter in the selected district scope.',
            ],
        ];
    }

    private function buildVoterListReport(?District $selectedDistrict): array
    {
        $users = User::query()
            ->with('district.electionSetting')
            ->where('role', 'user')
            ->when($selectedDistrict, fn ($query) => $query->where('district_id', $selectedDistrict->id))
            ->orderBy('name')
            ->get();

        $approvedRows = $users
            ->where('status', 'approved')
            ->map(fn (User $user) => [
                $user->name,
                $user->citizenship_number,
                $user->district?->name ?? $user->last_known_district_name ?? 'Not assigned',
                $this->electionTitleForDistrict($user->district),
                $user->contact_number,
                $user->voter_id_number,
                $user->hasVoted() ? 'Voted' : 'Not Voted',
            ])
            ->values()
            ->all();

        $reviewRows = $users
            ->whereIn('status', ['pending', 'rejected'])
            ->map(fn (User $user) => [
                $user->name,
                ucfirst($user->status),
                $user->district?->name ?? $user->last_known_district_name ?? 'Not assigned',
                $this->electionTitleForDistrict($user->district),
                $user->contact_number,
                $user->citizenship_number,
                'Not Voted',
                $user->rejection_message ?: '-',
            ])
            ->values()
            ->all();

        return [
            'title' => 'Voter List Report',
            'context' => $this->reportContext($selectedDistrict),
            'generated_at' => now()->format('M d, Y h:i A'),
            'summary' => [
                ['label' => 'Election Title', 'value' => $this->reportElectionTitleValue($selectedDistrict)],
                ['label' => 'Approved voters', 'value' => count($approvedRows)],
                ['label' => 'Pending voters', 'value' => $users->where('status', 'pending')->count()],
                ['label' => 'Rejected voters', 'value' => $users->where('status', 'rejected')->count()],
                ['label' => 'Approved voters who voted', 'value' => $users->where('status', 'approved')->filter(fn (User $user) => $user->hasVoted())->count()],
                ['label' => 'Total voter records', 'value' => $users->count()],
            ],
            'sections' => [
                [
                    'title' => 'Approved Voters',
                    'headers' => ['Name', 'Citizenship No.', 'District', 'Election Title', 'Contact Number', 'Voter ID', 'Voting Status'],
                    'rows' => $approvedRows,
                ],
                [
                    'title' => 'Pending And Rejected Voters',
                    'headers' => ['Name', 'Status', 'District', 'Election Title', 'Contact Number', 'Citizenship No.', 'Voting Status', 'Message'],
                    'rows' => $reviewRows,
                ],
            ],
            'notes' => [
                'Contact details are included so admins can follow up with pending or rejected voters.',
            ],
        ];
    }

    private function buildCandidateReport(?District $selectedDistrict): array
    {
        $candidates = Candidate::query()
            ->with(['district.electionSetting', 'votes'])
            ->when($selectedDistrict, fn ($query) => $query->where('district_id', $selectedDistrict->id))
            ->orderBy('name')
            ->get();

        $candidateRows = $candidates
            ->map(fn (Candidate $candidate) => [
                $candidate->name,
                $candidate->party ?: 'Independent',
                $candidate->district?->name ?? 'Unknown District',
                $this->electionTitleForDistrict($candidate->district),
                $candidate->position,
                $candidate->email,
                $candidate->votes->count(),
            ])
            ->values()
            ->all();

        $comparisonRows = $candidates
            ->sortByDesc(fn (Candidate $candidate) => $candidate->votes->count())
            ->values()
            ->map(fn (Candidate $candidate, int $index) => [
                $index + 1,
                $candidate->name,
                $candidate->district?->name ?? 'Unknown District',
                $this->electionTitleForDistrict($candidate->district),
                $candidate->party ?: 'Independent',
                $candidate->votes->count(),
            ])
            ->all();

        return [
            'title' => 'Candidate Report',
            'context' => $this->reportContext($selectedDistrict),
            'generated_at' => now()->format('M d, Y h:i A'),
            'summary' => [
                ['label' => 'Election Title', 'value' => $this->reportElectionTitleValue($selectedDistrict)],
                ['label' => 'Candidates listed', 'value' => $candidates->count()],
                ['label' => 'District coverage', 'value' => $candidates->pluck('district_id')->filter()->unique()->count()],
                ['label' => 'Total votes across candidates', 'value' => $candidates->sum(fn (Candidate $candidate) => $candidate->votes->count())],
            ],
            'sections' => [
                [
                    'title' => 'Candidate Profiles',
                    'headers' => ['Name', 'Party', 'District', 'Election Title', 'Position', 'Contact Email', 'Votes'],
                    'rows' => $candidateRows,
                ],
                [
                    'title' => 'Candidate Comparison Table',
                    'description' => 'This table can be opened in Excel for side-by-side vote comparison.',
                    'headers' => ['Rank', 'Candidate', 'District', 'Election Title', 'Party', 'Votes'],
                    'rows' => $comparisonRows,
                ],
            ],
            'notes' => [
                'Candidates without a party value are marked as Independent in the export.',
            ],
        ];
    }

    private function buildAuditLogReport(?District $selectedDistrict): array
    {
        $logs = AuditLog::query()
            ->with(['user', 'district.electionSetting'])
            ->when($selectedDistrict, fn ($query) => $query->where('district_id', $selectedDistrict->id))
            ->latest('logged_at')
            ->limit(500)
            ->get();

        $rows = $logs
            ->map(fn (AuditLog $log) => [
                optional($log->logged_at)->format('M d, Y h:i:s A') ?? '-',
                $log->user?->name ?? 'System',
                str($log->action)->replace('_', ' ')->title()->toString(),
                $log->district?->name ?? ($selectedDistrict?->name ?? 'General'),
                $this->electionTitleForDistrict($log->district ?? $selectedDistrict),
                $log->ip_address ?? 'Unknown',
                $log->description,
            ])
            ->values()
            ->all();

        return [
            'title' => 'Audit Log Report',
            'context' => $this->reportContext($selectedDistrict),
            'generated_at' => now()->format('M d, Y h:i A'),
            'summary' => [
                ['label' => 'Election Title', 'value' => $this->reportElectionTitleValue($selectedDistrict)],
                ['label' => 'Log entries exported', 'value' => count($rows)],
                ['label' => 'Unique actors', 'value' => $logs->pluck('user_id')->filter()->unique()->count()],
                ['label' => 'Latest activity', 'value' => optional($logs->first()?->logged_at)->format('M d, Y h:i A') ?? 'No activity yet'],
            ],
            'sections' => [
                [
                    'title' => 'Security Review Timeline',
                    'headers' => ['Timestamp', 'Actor', 'Action', 'District', 'Election Title', 'IP Address', 'Description'],
                    'rows' => $rows,
                ],
            ],
            'notes' => [
                'The audit log records report exports, login success, vote casting, and key admin actions from this update forward.',
            ],
        ];
    }

    private function buildElectionSummaryReport(?District $selectedDistrict): array
    {
        $districts = $this->districtScope($selectedDistrict)
            ->load(['votes', 'users', 'electionSetting']);

        $archives = ElectionArchive::query()
            ->when($selectedDistrict, fn ($query) => $query->where('district_name', $selectedDistrict->name))
            ->get();

        $activeCount = $districts->filter(fn (District $district) => $district->electionSetting?->is_active)->count();
        $completedCount = $districts->filter(fn (District $district) => $district->electionSetting?->hasEnded())->count() + $archives->count();
        $approvedVoters = $districts->sum(fn (District $district) => $district->users->where('status', 'approved')->count());
        $votesCast = $districts->sum(fn (District $district) => $district->votes->count());
        $participation = $approvedVoters > 0 ? round(($votesCast / max($approvedVoters * 2, 1)) * 100, 2) : 0;

        $districtRows = $districts->map(function (District $district) {
            $approvedVoters = $district->users->where('status', 'approved')->count();
            $votesCast = $district->votes->count();
            $turnout = $approvedVoters > 0 ? round(($votesCast / max($approvedVoters * 2, 1)) * 100, 2) : 0;

            return [
                $district->name,
                $this->electionTitleForDistrict($district),
                $district->electionSetting?->is_active ? 'Active' : ($district->electionSetting?->hasEnded() ? 'Completed' : ($district->electionSetting?->isPaused() ? 'Paused' : 'Pending')),
                $votesCast,
                $approvedVoters,
                number_format($turnout, 2).'%',
            ];
        })->values()->all();

        return [
            'title' => 'Election Summary Report',
            'context' => $this->reportContext($selectedDistrict),
            'generated_at' => now()->format('M d, Y h:i A'),
            'summary' => [
                ['label' => 'Election Title', 'value' => $this->reportElectionTitleValue($selectedDistrict)],
                ['label' => 'Total elections conducted', 'value' => $completedCount + $activeCount],
                ['label' => 'Active elections', 'value' => $activeCount],
                ['label' => 'Completed elections', 'value' => $completedCount],
                ['label' => 'Overall participation', 'value' => number_format($participation, 2).'%'],
            ],
            'sections' => [
                [
                    'title' => 'District Election Status',
                    'headers' => ['District', 'Election Title', 'Status', 'Votes Cast', 'Approved Voters', 'Participation'],
                    'rows' => $districtRows,
                ],
                [
                    'title' => 'Archived Election Totals',
                    'headers' => ['Archived District', 'Election Title', 'Archive Reason', 'Votes', 'Candidates', 'Deleted At'],
                    'rows' => $archives->map(fn (ElectionArchive $archive) => [
                        $archive->district_name,
                        $archive->election_title ?: 'Not set',
                        ucfirst(str_replace('_', ' ', $archive->archive_reason)),
                        $archive->total_votes,
                        $archive->candidate_count,
                        optional($archive->deleted_at)->format('M d, Y h:i A') ?? '-',
                    ])->values()->all(),
                ],
            ],
            'notes' => [
                'Participation is based on votes recorded against the total possible votes from approved voters in the selected scope.',
            ],
        ];
    }

    private function districtScope(?District $selectedDistrict): Collection
    {
        return District::query()
            ->when($selectedDistrict, fn ($query) => $query->whereKey($selectedDistrict->id))
            ->orderBy('name')
            ->get();
    }

    private function reportContext(?District $selectedDistrict): string
    {
        return 'District Filter: '.($selectedDistrict?->name ?? 'All Districts').' | Election Title: '.$this->reportElectionTitleValue($selectedDistrict);
    }

    private function electionTitleForDistrict(?District $district): string
    {
        return $district?->electionSetting?->election_title ?: 'Not set';
    }

    private function reportElectionTitleValue(?District $selectedDistrict): string
    {
        if ($selectedDistrict) {
            $selectedDistrict->loadMissing('electionSetting');

            return $this->electionTitleForDistrict($selectedDistrict);
        }

        $titles = District::query()
            ->with('electionSetting')
            ->orderBy('name')
            ->get()
            ->map(function (District $district) {
                $title = $district->electionSetting?->election_title;

                return $title ? "{$district->name}: {$title}" : null;
            })
            ->filter()
            ->values();

        return $titles->isNotEmpty() ? $titles->implode(' | ') : 'Not set';
    }

    private function reportCards(): array
    {
        return [
            'election-results' => [
                'title' => 'Election Results Report',
                'description' => 'District wise vote totals, candidate counts, winners, runner up details, and turnout percentage.',
            ],
            'voter-list' => [
                'title' => 'Voter List Report',
                'description' => 'Approved voter list plus pending and rejected voter records with contact details.',
            ],
            'candidate-report' => [
                'title' => 'Candidate Report',
                'description' => 'Candidate profile export with party, district, position, votes, and comparison table.',
            ],
            'audit-log' => [
                'title' => 'Audit Log Report',
                'description' => 'Security review export of user actions, timestamps, IP addresses, and action summaries.',
            ],
            'election-summary' => [
                'title' => 'Election Summary Report',
                'description' => 'Overall election activity, active versus completed counts, and participation snapshots.',
            ],
        ];
    }
}
