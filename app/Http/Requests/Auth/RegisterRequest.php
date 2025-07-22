<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

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
        'username' => ['required', 'string', 'max:255', 'unique:users,username'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
        'password' => [
            'required',
            'confirmed',
            Password::min(8)
                ->mixedCase()   // huruf besar & kecil
                ->letters()     // harus ada huruf
                ->numbers(),    // harus ada angka
        ],
    ];
}

   public function messages(): array
{
    return [
        'password.min' => 'Password minimal 8 karakter.',
        'password.mixedCase' => 'Password harus mengandung huruf besar dan kecil.',
        'password.letters' => 'Password harus mengandung huruf.',
        'password.numbers' => 'Password harus mengandung angka.',
    ];
}

}
