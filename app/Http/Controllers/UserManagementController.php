<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserUpdateRequest;
use App\Models\District;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(): View
    {
        $districtId = request('district_id');
        $showAll = request()->boolean('show_all');

        $users = User::query()
            ->with('district')
            ->where('role', 'user')
            ->where('status', 'approved')
            ->when($districtId, fn ($query) => $query->where('district_id', $districtId))
            ->when(
                $showAll,
                fn ($query) => $query
                    ->join('districts', 'districts.id', '=', 'users.district_id')
                    ->orderBy('districts.name')
                    ->orderBy('users.name')
                    ->select('users.*'),
                fn ($query) => $query->orderBy('name')
            )
            ->paginate(15)
            ->withQueryString();

        return view('users.index', [
            'users' => $users,
            'districts' => District::query()->orderBy('name')->get(),
            'selectedDistrict' => $districtId,
            'showAll' => $showAll,
        ]);
    }

    public function update(UserUpdateRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        if (isset($data['district_id'])) {
            $data['last_known_district_name'] = District::query()->find($data['district_id'])?->name;
        }

        $user->update($data);

        return back()->with('status', 'User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return back()->with('status', 'User deleted successfully.');
    }
}
