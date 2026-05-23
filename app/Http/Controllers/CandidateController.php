<?php

namespace App\Http\Controllers;

use App\Http\Requests\CandidateRequest;
use App\Models\Candidate;
use App\Models\DeletedCandidate;
use App\Models\District;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CandidateController extends Controller
{
    public function index(): View
    {
        $districtId = request('district_id');

        $candidates = Candidate::query()
            ->with('district')
            ->when($districtId, fn ($query) => $query->where('district_id', $districtId))
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        return view('candidates.index', [
            'candidates' => $candidates,
            'districts' => District::query()->orderBy('name')->get(),
            'selectedDistrict' => $districtId,
        ]);
    }

    public function create(): View
    {
        return view('candidates.form', [
            'candidate' => new Candidate(),
            'districts' => District::query()->orderBy('name')->get(),
            'formAction' => route('candidates.store'),
            'formMethod' => 'POST',
            'pageTitle' => 'Add Candidate',
        ]);
    }

    public function store(CandidateRequest $request): RedirectResponse
    {
        $candidate = new Candidate($request->safe()->except(['image', 'vision']));
        $candidate->image_path = $request->file('image')?->store('candidates/images', 'public');
        $candidate->vision_path = $request->file('vision')?->store('candidates/visions', 'public');
        $candidate->position = $request->input('position', 'District Representative');
        $candidate->save();

        return redirect()->route('candidates.index')->with('status', 'Candidate added successfully.');
    }

    public function edit(Candidate $candidate): View
    {
        return view('candidates.form', [
            'candidate' => $candidate,
            'districts' => District::query()->orderBy('name')->get(),
            'formAction' => route('candidates.update', $candidate),
            'formMethod' => 'PUT',
            'pageTitle' => 'Edit Candidate',
        ]);
    }

    public function update(CandidateRequest $request, Candidate $candidate): RedirectResponse
    {
        $data = $request->safe()->except(['image', 'vision']);
        $data['position'] = $request->input('position', 'District Representative');

        if ($request->hasFile('image')) {
            if ($candidate->image_path) {
                Storage::disk('public')->delete($candidate->image_path);
            }

            $data['image_path'] = $request->file('image')->store('candidates/images', 'public');
        }

        if ($request->hasFile('vision')) {
            if ($candidate->vision_path) {
                Storage::disk('public')->delete($candidate->vision_path);
            }

            $data['vision_path'] = $request->file('vision')->store('candidates/visions', 'public');
        }

        $candidate->update($data);

        return redirect()->route('candidates.index')->with('status', 'Candidate updated successfully.');
    }

    public function destroy(Candidate $candidate): RedirectResponse
    {
        DeletedCandidate::query()->create([
            'original_candidate_id' => $candidate->id,
            'district_name' => $candidate->district?->name ?? 'Unknown District',
            'candidate_name' => $candidate->name,
            'age' => $candidate->age,
            'position' => $candidate->position,
            'email' => $candidate->email,
            'image_path' => $candidate->image_path,
            'vision_path' => $candidate->vision_path,
            'vote_count' => $candidate->votes()->count(),
            'deleted_reason' => 'candidate_deleted',
            'deleted_at' => now(),
        ]);

        if ($candidate->image_path) {
            Storage::disk('public')->delete($candidate->image_path);
        }

        if ($candidate->vision_path) {
            Storage::disk('public')->delete($candidate->vision_path);
        }

        $candidate->delete();

        return redirect()->route('candidates.index')->with('status', 'Candidate deleted successfully.');
    }
}
