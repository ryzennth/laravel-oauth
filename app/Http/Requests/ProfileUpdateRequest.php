<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
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
            Rule::unique(User::class, 'username')->ignore($this->user()->id),
        ],
        'email' => [
            'required',
            'string',
            'lowercase',
            'email',
            'max:255',
            Rule::unique(User::class)->ignore($this->user()->id),
        ],
        'password' => [
            'nullable',
            'confirmed',
            Password::min(8)
                ->mixedCase()   // huruf besar & kecil
                ->letters()     // harus ada huruf
                ->numbers()    // harus ada angka
                ->symbols()    //harus ada simbol
        ],
    ];
}
public function messages(): array
{
    return [
        'username.regex' => 'Username hanya boleh huruf kecil dan angka tanpa spasi atau simbol.',
        'username.unique' => 'Username sudah digunakan.',
        'email.unique' => 'Email sudah digunakan.',
        'password.min' => 'Password minimal harus 8 karakter.',
        'password.mixed' => 'Password harus mengandung huruf besar dan kecil.',
        'password.letters' => 'Password harus mengandung huruf.',
        'password.numbers' => 'Password harus mengandung angka.',
        'password.symbols' => 'Password harus mengandung simbol (seperti !@#$%).',
        'password.confirmed' => 'Konfirmasi password tidak cocok.',

    ];
}


}

