<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Tandai email pengguna sebagai sudah diverifikasi.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            // Redirect ke login dengan parameter "verified=1"
            return redirect()->route('login')->with('status', 'Email sudah diverifikasi.');
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        // Logout user supaya kembali ke login setelah verifikasi
        auth()->logout();

        return redirect()->route('login')->with('status', 'Email berhasil diverifikasi. Silakan login.');
    }
}
