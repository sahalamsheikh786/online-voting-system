<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\DeletedCandidate;
use App\Models\District;
use App\Models\ElectionArchive;
use App\Models\ElectionSetting;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DistrictController extends Controller
{
    public function create(): View
    {
        return view('districts.create', [
            'districts' => District::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'regex:/^[A-Za-z\s]+$/', 'max:255', 'unique:districts,name'],
        ], [
            'name.regex' => 'District name must contain only alphabet characters and spaces.',
        ]);

        $district = District::create($validated);

        ElectionSetting::firstOrCreate(
            ['district_id' => $district->id],
            [
                'is_active' => false,
                'started_at' => null,
                'ends_at' => null,
            ]
        );

        return redirect()->route('districts.create')->with('status', 'Election card added successfully.');
    }

    public function hardDelete(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'district_id' => ['required', 'exists:districts,id'],
        ], [
            'district_id.required' => 'Please select a district card to delete.',
        ]);

        $district = District::query()->findOrFail((int) $validated['district_id']);
        $districtName = $district->name;

        DB::transaction(function () use ($district, $districtName) {
            User::query()
                ->where('district_id', $district->id)
                ->orWhere('last_known_district_name', $districtName)
                ->update([
                    'district_id' => null,
                    'last_known_district_name' => null,
                    'has_voted_at' => null,
                ]);

            AuditLog::query()->where('district_id', $district->id)->delete();
            DeletedCandidate::query()->where('district_name', $districtName)->delete();
            ElectionArchive::query()->where('district_name', $districtName)->delete();
            ElectionSetting::query()->where('district_id', $district->id)->delete();
            $district->delete();
        });

        return redirect()
            ->route('districts.create')
            ->with('status', "{$districtName} district card was permanently deleted from the system.");
    }
}
