<?php


use App\Models\User;
use Inertia\Inertia;
use App\Models\Article;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Auth\Events\Registered;
use App\Http\Controllers\RoleController;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\SetPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Admin\ArticleModerationController;


Route::get('/', function () {
    $articles = Article::with('user')->latest()->take(5)->get();
    return Inertia::render('Welcome', [
        'articles' => $articles
    ]);
})->name('welcome');

// Authenticated & verified user routes
Route::middleware(['auth', 'verified', 'ensure.password', 'complete.profile'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard', [
            'articles' => Article::where('status', 'published')->get()
        ]);
    })->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/photo', [ProfileController::class, 'updatePhoto'])->name('profile.photo.update');
    Route::get('/articles/{article}', [ArticleController::class, 'show'])->name('articles.show');
});

// Google OAuth
Route::get('/auth/google/redirect', fn () => Socialite::driver('google')->redirect())->name('auth.google.redirect');
Route::get('/auth/google/callback', function () {
    $googleUser = Socialite::driver('google')->stateless()->user();
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
    if (auth()->check()) return redirect()->route('dashboard');
    $oauthData = session('google_oauth');
    if (!$oauthData) return redirect()->route('login');
    return Inertia::render('Auth/GoogleVerify', ['googleUser' => $oauthData]);
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

// Set password for users without password
Route::middleware(['auth'])->group(function () {
    Route::get('/set-password', [SetPasswordController::class, 'create'])->name('password.set');
    Route::post('/set-password', [SetPasswordController::class, 'store'])->name('password.set.store');
});

// Forgot / Reset Password
Route::middleware('guest')->group(function () {
    Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update');
});

// Super Admin routes
Route::prefix('admin')->middleware(['auth', 'role:super admin'])->name('admin.')->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('permissions', PermissionController::class);
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::get('/user-roles', [UserRoleController::class, 'index'])->name('user.roles.index');
    Route::post('/user-roles/{user}/assign', [UserRoleController::class, 'assign'])->name('user.roles.assign');
    Route::put('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
});

// Penulis routes
Route::middleware(['auth', 'role:penulis'])->group(function () {
    Route::get('/articles/create/write', [ArticleController::class, 'create'])->name('articles.create');
    Route::post('/articles', [ArticleController::class, 'store'])->name('articles.store');
});


Route::post('/upload-image', [\App\Http\Controllers\UploadController::class, 'store'])->name('upload.image');

// routes/web.php
Route::middleware(['auth', 'role:penulis'])->group(function () {
    // Penulis
    Route::get('/author/articles', [ArticleController::class, 'listPenulis'])->name('author.articles.index');
    Route::patch('/author/articles/{article}/resubmit', [ArticleController::class, 'resubmit'])->name('author.articles.resubmit');
    Route::get('/author/articles/{article}/edit', [ArticleController::class, 'edit'])->name('articles.edit');
    Route::put('/author/articles/{article}', [ArticleController::class, 'update'])->name('author.articles.update');
    Route::delete('/articles/{article}', [ArticleController::class, 'destroy'])->name('author.articles.destroy');


});
    // Admin
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/articles', [ArticleController::class, 'listAdmin'])->name('admin.articles.index');
        Route::patch('/admin/articles/{article}/approve', [ArticleModerationController::class, 'approve'])->name('admin.articles.approve');
        Route::patch('/admin/articles/{article}/reject', [ArticleModerationController::class, 'reject'])->name('admin.articles.reject');
    });



require __DIR__.'/auth.php';