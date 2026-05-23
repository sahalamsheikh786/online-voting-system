<?php

namespace App\Http\Controllers;

use App\Http\Requests\PendingUserActionRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PendingUserController extends Controller
{
    public function index(): View
    {
        return view('pending-users.index', [
            'pendingUsers' => User::query()
                ->with('district')
                ->where('role', 'user')
                ->where('status', 'pending')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function update(PendingUserActionRequest $request, User $user): RedirectResponse
    {
        if ($request->string('action')->toString() === 'accept') {
            $user->update([
                'status' => 'approved',
                'approved_at' => now(),
                'rejection_message' => null,
            ]);

            return back()->with('status', 'User accepted successfully.');
        }

        $user->update([
            'status' => 'rejected',
            'rejection_message' => $request->input('rejection_message', 'You can try once again'),
        ]);

        return back()->with('status', 'User rejected successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return back()->with('status', 'Pending user deleted successfully.');
    }
}
