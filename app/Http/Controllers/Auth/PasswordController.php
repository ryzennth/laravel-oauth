<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request)
{
    $validated = $request->validate([
        'current_password' => ['required', 'current_password'],
        'password' => [
            'required',
            'confirmed',
            Password::min(8)
                ->mixedCase()
                ->letters()
                ->numbers(),
        ],
    ]);

    $request->user()->update([
        'password' => bcrypt($validated['password']),
    ]);
    $validated = $request->validate([
    // validasi di sini
    ], [
        'password.min' => 'Password minimal 8 karakter.',
        'password.mixedCase' => 'Password harus mengandung huruf besar dan kecil.',
        'password.letters' => 'Password harus mengandung huruf.',
        'password.numbers' => 'Password harus mengandung angka.',
        'password.confirmed' => 'Konfirmasi password tidak cocok.',
        'current_password.current_password' => 'Password saat ini salah.',
    ]);


    return back()->with('status', 'password-updated');
}
}
