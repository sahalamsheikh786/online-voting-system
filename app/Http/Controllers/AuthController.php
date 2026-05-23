<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Models\District;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        if (! Auth::attempt(['contact_number' => $credentials['contact_number'], 'password' => $credentials['password']], $request->boolean('remember'))) {
            return back()->withErrors([
                'contact_number' => 'The provided credentials do not match our records.',
            ])->onlyInput('contact_number');
        }

        $request->session()->regenerate();

        $user = $request->user();

        if (! $user->pattern_lock || ! Hash::check($credentials['pattern_lock'], $user->pattern_lock)) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'pattern_lock' => 'Pattern lock does not match our records.',
            ])->onlyInput('contact_number');
        }

        if (! $user->isAdmin() && ! $user->isApproved()) {
            Auth::logout();

            return redirect()->route('login')->with('status', $user->status === 'rejected'
                ? ($user->rejection_message ?: 'Your registration was rejected. You can try once again.')
                : 'Your account is pending approval. Please wait a little longer.');
        }

        return redirect()->intended($user->isAdmin() ? route('dashboard') : route('vote.index'));
    }

    public function showRegister(): View
    {
        return view('auth.register', [
            'districts' => District::query()->orderBy('name')->get(),
        ]);
    }

    public function register(RegisterUserRequest $request): RedirectResponse
    {
        $imagePath = $request->file('image')->store('users', 'public');

        User::create([
            'name' => $request->string('name')->toString(),
            'contact_number' => $request->string('contact_number')->toString(),
            'password' => Hash::make($request->string('password')->toString()),
            'pattern_lock' => $request->string('pattern_lock')->toString(),
            'role' => 'user',
            'status' => 'pending',
            'date_of_birth' => $request->date('date_of_birth'),
            'district_id' => (int) $request->district_id,
            'last_known_district_name' => District::query()->find($request->district_id)?->name,
            'citizenship_number' => $request->string('citizenship_number')->toString(),
            'voter_id_number' => $request->string('voter_id_number')->toString(),
            'image_path' => $imagePath,
        ]);

        return redirect()
            ->route('login')
            ->with('status', 'Registration submitted successfully. Please wait for admin approval.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
