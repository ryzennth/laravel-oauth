<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
{
    $user = $request->user();

    return Inertia::render('Profile/Edit', [
        'mustVerifyEmail' => $user instanceof MustVerifyEmail,
        'status' => session('status'),
        'user' => $user,
        'mustSetPassword' => is_null($user->password),
    ]);
}

    

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
{
    $user = $request->user();
    $validated = $request->validated();

    if ($user->isDirty('email')) {
        $user->email_verified_at = null;
    }

    $user->fill($validated);

    if ($request->filled('password')) {
        $user->password = bcrypt($validated['password']);
    }

    $user->save();

    return Redirect::route('profile.edit')->with('status', 'profile-updated');
}



    public function rules(): array
{
    return [
        'name' => ['required', 'string', 'max:255'],
        'username' => ['required', 'string', 'max:255', 'unique:users,username,' . $this->user()->id],
        'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $this->user()->id],
    ];
}


    /**
     * Update the user's profile photo.
     */
    public function updatePhoto(Request $request): RedirectResponse
    {
        $request->validate([
            'profile_photo' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $user = Auth::user();
        $file = $request->file('profile_photo');
        $path = $file->store('profile_photos', 'public');

        // Hapus foto lama jika ada
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        // Simpan path foto baru
        $user->update(['profile_photo_path' => $path]);

        return Redirect::route('profile.edit')->with('status', 'profile-photo-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Hapus foto profil jika ada
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}