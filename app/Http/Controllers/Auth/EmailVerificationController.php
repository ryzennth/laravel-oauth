<?php
namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

class EmailVerificationController extends \App\Http\Controllers\Controller
{
    public function __invoke(EmailVerificationRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect($this->redirectTo($request));
        }

        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect($this->redirectTo($request));
    }

    protected function redirectTo(Request $request)
    {
        // Jika belum punya password
        if (is_null($request->user()->password)) {
            return route('password.set');
        }

        return route('dashboard');
    }
}
