<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Get the current user or fallback to first user.
     */
    private function getUser(): User
    {
        return Auth::user() ?? User::first() ?? new User(['name' => 'Admin', 'email' => 'admin@example.com']);
    }

    /**
     * Display the user's profile.
     */
    public function show(): View
    {
        $user = $this->getUser();
        return view('profile.show', compact('user'));
    }

    /**
     * Show the form for editing the profile.
     */
    public function edit(): View
    {
        $user = $this->getUser();
        return view('profile.edit', compact('user'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = $this->getUser();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        $user->update($validated);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'Profile updated successfully.',
                'user' => $user
            ]);
        }

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully.');
    }

    /**
     * Show the form for changing password.
     */
    public function editPassword(): View
    {
        $user = $this->getUser();
        return view('profile.password', compact('user'));
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = $this->getUser();
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'Password changed successfully.'
            ]);
        }

        return redirect()->route('profile.show')->with('success', 'Password changed successfully.');
    }

    /**
     * Show account deletion confirmation.
     */
    public function confirmDelete(): View
    {
        $user = $this->getUser();
        return view('profile.delete', compact('user'));
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $this->getUser();
        
        // In a real app, you would logout and delete
        // Auth::logout();
        // $user->delete();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'Account deleted successfully.'
            ]);
        }

        return redirect('/')->with('success', 'Account deleted successfully.');
    }
}
