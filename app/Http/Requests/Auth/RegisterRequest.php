<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }



public function rules(): array
{
    return [
        'name' => ['required', 'string', 'max:255'],
        'username' => [
            'required',
            'string',
            'min:3',
            'max:20',
            'regex:/^[a-z0-9]+$/',
            Rule::unique(User::class, 'username'),
        ],
        'email' => [
            'required',
            'string',
            'lowercase',
            'email',
            'max:255',
            Rule::unique(User::class, 'email'),
        ],
        'password' => [
            'required',
            'confirmed',
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
        'password.min' => 'Password minimal 8 karakter.',
        'password.mixedCase' => 'Password harus mengandung huruf besar dan kecil.',
        'password.letters' => 'Password harus mengandung huruf.',
        'password.numbers' => 'Password harus mengandung angka.',
        'password.symbols' => 'Password harus mengandung symbol.',
    ];
}

}
