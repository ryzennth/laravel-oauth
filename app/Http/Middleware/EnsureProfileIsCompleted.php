<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureProfileIsCompleted
{
    public function handle(Request $request, Closure $next)
{
    $user = Auth::user();

    if ($user) {
        $missingPassword = empty($user->password);
        $missingUsername = empty($user->username);

        // Bypass jika user adalah super admin
        if ($user->hasRole('super admin')) {
            return $next($request);
        }

        // Rute yang dibebaskan
        $allowedRoutes = [
            'profile',
            'profile/*',
            'set-password',
            'set-password/*',
            'logout',          // ← Tambahin logout juga biar gak nyangkut
            'admin/users/create',
        ];

        $currentPath = $request->path();
        $isAllowed = collect($allowedRoutes)->contains(function ($route) use ($currentPath) {
            return str($currentPath)->is($route);
        });

        if (($missingPassword || $missingUsername) && ! $isAllowed) {
            // Flash message tergantung kondisi
            $flashes = [];
            if ($missingPassword) $flashes['mustSetPassword'] = true;
            if ($missingUsername) $flashes['mustCompleteProfile'] = true;

            return redirect()->route('profile.edit')->with($flashes);
        }
    }

    return $next($request);
}
}