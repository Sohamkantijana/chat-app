<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Models\User;
use App\Models\UserStatus;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user()->load('statuses'); // eager load statuses
        return view('profile.edit', compact('user'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        // Update text status if present
        if ($request->has('status')) {
            $user->status = $request->status;
        }

        $user->save();

        // Handle multiple status images upload
        if ($request->hasFile('status_images')) {
            foreach ($request->file('status_images') as $file) {
                $request->validate([
                    'status_images.*' => 'image|mimes:jpeg,jpg,png,gif|max:2048', // 2MB max
                ]);

                $path = $file->store('status_images', 'public');

                UserStatus::create([
                    'user_id' => $user->id,
                    'image' => $path,
                ]);
            }
        }

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Show another user's profile (for viewing status images).
     */
    public function show($id): View
    {
        $user = User::with('statuses')->findOrFail($id); // eager load statuses
        return view('profile.show', compact('user'));
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
