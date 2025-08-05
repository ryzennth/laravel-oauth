<?php

use App\Http\Controllers\RoleController;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Auth\Events\Registered;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\SetPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;

Route::get('/', fn () => redirect('/login'));

// ------------------------
// Google Login
// ------------------------
Route::get('/auth/google/redirect', fn () => Socialite::driver('google')->redirect())->name('auth.google.redirect');

Route::get('/auth/google/callback', function () {
    $googleUser = Socialite::driver('google')->stateless()->user();

    // Simpan data sementara di session
    session([
        'google_oauth' => [
            'id' => $googleUser->getId(),
            'name' => $googleUser->getName(),
            'email' => $googleUser->getEmail(),
            'avatar' => $googleUser->getAvatar(),
            
        ]
    ]);
    return redirect()->route('google.verify');
});

Route::get('/google/verify', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    $oauthData = session('google_oauth');
    if (!$oauthData) {
        return redirect()->route('login');
    }

    return Inertia::render('Auth/GoogleVerify', [
        'googleUser' => $oauthData,
    ]);
})->name('google.verify');


Route::post('/google/confirm', function () {
    $oauth = session('google_oauth');

    if (!$oauth) return redirect()->route('login');

    $user = User::where('google_id', $oauth['id'])->orWhere('email', $oauth['email'])->first();

    if (!$user) {
        $user = User::create([
            'name' => $oauth['name'] ?? 'Google User',
            'email' => $oauth['email'],
            'google_id' => $oauth['id'],
            'username' => null,
            'password' => null,
            'email_verified_at' => now(),
        ]);

        event(new Registered($user));
    } elseif (!$user->google_id) {
        $user->google_id = $oauth['id'];
        $user->save();
    }

    Auth::login($user);
    session()->forget('google_oauth');

    return redirect()->route('profile.edit');
})->name('google.confirm');

// ------------------------
// Routes yang memerlukan password (terkunci jika belum set)
// ------------------------
Route::middleware(['auth', 'verified', 'ensure.password'])->group(function () {
    Route::middleware(['complete.profile'])->group(function () {
        Route::get('/dashboard', fn () => Inertia::render('Dashboard'))->name('dashboard');
    });

    // Profile (edit/update/hapus)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo.update');
});

// ------------------------
// Route yang dibuka untuk user TANPA password
// ------------------------
Route::middleware(['auth'])->group(function () {
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

Route::middleware(['auth', 'role:super admin'])->group(function () {
    Route::get('/admin/user-roles', [UserRoleController::class, 'index'])->name('user.roles.index');
    Route::post('/admin/user-roles/{user}/assign', [UserRoleController::class, 'assign'])->name('user.roles.assign');
});
Route::middleware(['auth', 'role:super admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    });

    Route::middleware(['auth', 'verified', 'complete.profile', 'role:super admin'])->group(function () {
    Route::get('/admin/users/create', [UserController::class, 'create'])->name('admin.users.create');
});

Route::middleware(['auth', 'verified', 'role:super admin'])->prefix('admin')->group(function () {
    Route::get('/users/create', [UserController::class, 'create'])->name('admin.users.create');
    Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
});

Route::prefix('admin')->middleware(['auth', 'role:super admin'])->group(function () {
    Route::get('/roles', [RoleController::class, 'index'])->name('admin.roles.index');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('admin.roles.edit');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('admin.roles.update');
});




require __DIR__.'/auth.php';
