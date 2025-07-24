<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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
        $user = $request->user();

        $rules = [
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->mixedCase()->letters()->numbers(),
            ],
        ];

        // Jika user sudah punya password, wajib isi current_password
        if ($user->password) {
            $rules['current_password'] = ['required', 'current_password'];
        } else {
            $rules['current_password'] = ['nullable'];
        }

        $validated = $request->validate($rules, [
            'password.min' => 'Password minimal 8 karakter.',
            'password.mixedCase' => 'Password harus mengandung huruf besar dan kecil.',
            'password.letters' => 'Password harus mengandung huruf.',
            'password.numbers' => 'Password harus mengandung angka.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'current_password.current_password' => 'Password saat ini salah.',
        ]);

        // Simpan password baru
        $user->update([
            'password' => bcrypt($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }
}
