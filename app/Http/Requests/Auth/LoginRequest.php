<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'login' => ['required', 'string'],
            'password' => [
            'required',
            
            Password::min(8)
                ->mixedCase()   // huruf besar & kecil
                ->letters()     // harus ada huruf
                ->numbers()     // harus ada angka
                ->symbols()    // harus ada simbol                
            ],
            'g-recaptcha-response' => ['required', function ($attribute, $value, $fail) {
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => env('RECAPTCHA_SECRET_KEY'),
            'response' => $value,
        ]);
        if (!$response->json('success')) {
            $fail('CAPTCHA tidak valid.');
        }
    }],
        ];
    }

    public function messages(): array
    {
        return [
            'password.regex' => 'Password harus mengandung lowercase, uppercase, angka, dan simbol.',
            'password.min' => 'Password minimal 8 karakter.',
        ];
    }

    public function authenticate(): void
{
    $this->ensureIsNotRateLimited();

    $login = $this->input('login');
    $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

    // Cek user terlebih dahulu
    $user = User::where($field, $login)->first();

    if ($user && $user->google_id && is_null($user->password)) {
        throw ValidationException::withMessages([
            'login' => 'Akun ini terdaftar melalui Google. Silakan login dengan Google.',
        ]);
    }

    // ✅ Tambahkan pengecekan akun aktif/tidak
    if ($user && !$user->is_active) {
        // Flag buat SweetAlert di front-end
        session()->flash('account_deactivated', true);

        throw ValidationException::withMessages([
            'login' => 'Akun Anda telah dinonaktifkan.',
        ]);
    }

    // Coba autentikasi
    if (! Auth::attempt([$field => $login, 'password' => $this->input('password')], $this->boolean('remember'))) {
        RateLimiter::hit($this->throttleKey());

        throw ValidationException::withMessages([
            'login' => trans('auth.failed'),
        ]);
    }

    RateLimiter::clear($this->throttleKey());
}


    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'login' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('login')) . '|' . $this->ip());
    }
}
