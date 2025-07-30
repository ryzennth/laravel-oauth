<?php
use Illuminate\Support\Facades\Session;

public function handleGoogleCallback()
{
    $googleUser = Socialite::driver('google')->stateless()->user();

    // Simpan sementara data dari Google ke session
    Session::put('google_oauth', [
        'id' => $googleUser->id,
        'name' => $googleUser->name,
        'email' => $googleUser->email,
        'avatar' => $googleUser->avatar,
    ]);

    // Arahkan ke halaman verifikasi (step berikutnya)
    return redirect()->route('google.verify');
}

public function confirm(Request $request)
{
    $oauth = session('google_oauth');

    if (!$oauth) return redirect()->route('login');

    // Cari user berdasarkan google id
    $user = User::where('google_id', $oauth['id'])->first();

    if (!$user) {
        // Jika belum ada, buat user
        $user = User::create([
            'name' => $oauth['name'],
            'email' => $oauth['email'],
            'google_id' => $oauth['id'],
            'email_verified_at' => now(),
        ]);
    }

    Auth::login($user);

    // Hapus session
    session()->forget('google_oauth');

    // Redirect ke halaman update profile
    return redirect()->route('profile.edit');
}

