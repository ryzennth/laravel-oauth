<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsurePasswordIsSet
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Jika user login dan belum set password
        // Jika user belum punya password
if ($user && empty($user->password)) {
    // Izinkan akses hanya ke halaman tertentu
    if (! $request->is('profile') && ! $request->is('set-password') && ! $request->is('set-password/*')) {
        // Set flash di session (langsung manual, supaya bisa dibaca di HandleInertiaRequests)
        session()->flash('mustSetPassword', true);

        return redirect()->route('profile.edit');
    }
}


        return $next($request);
    }
}
