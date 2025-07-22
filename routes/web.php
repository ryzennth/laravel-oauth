<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\SetPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use PhpParser\Node\NullableType;

Route::get('/', fn () => redirect('/login'));

// ------------------------
// Google Login
// ------------------------
Route::get('/auth/google/redirect', fn () => Socialite::driver('google')->redirect())->name('auth.google.redirect');

Route::get('/auth/google/callback', function () {
    $googleUser = Socialite::driver('google')->stateless()->user();

    $user = User::where('email', $googleUser->getEmail())->first();

    if (! $user) {
        // Buat user baru TANPA password
        $user = User::create([
            'name' => $googleUser->getName() ?? 'Google User',
            'email' => $googleUser->getEmail(),
            'username' => Str::slug($googleUser->getName()) . rand(100, 999),
            'email_verified_at' => now(),
            'password' => null, 
        ]);

        Auth::login($user);
        return redirect()->route('password.set'); // redirect ke set password
    }

    // Jika user sudah ada tapi belum ada google_id
    if (! $user->google_id) {
        $user->google_id = $googleUser->getId();
    }

    if (is_null($user->email_verified_at)) {
        $user->email_verified_at = now();
    }

    $user->save();

    Auth::login($user);

    // Kalau password belum diset, paksa ke /set-password
    if (is_null($user->password)) {
        return redirect()->route('password.set');
    }

    return redirect()->intended('/dashboard');
});

// ------------------------
// Dashboard
// ------------------------
Route::get('/dashboard', function () {
    $user = auth()->user();

    // Jika password belum di-set (masih null / kosong)
    if (empty($user->getAuthPassword()) || is_null($user->password)) {
        return redirect()->route('password.set');
    }

    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


// ------------------------
// Profile
// ------------------------
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo.update');

    // Set Password
    Route::get('/set-password', [SetPasswordController::class, 'create'])->name('password.set');
    Route::post('/set-password', [SetPasswordController::class, 'store'])->name('password.set.store');
});

// ------------------------
// Forgot / Reset Password
// ------------------------
Route::middleware('guest')->group(function () {
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update');
});

require __DIR__.'/auth.php';
