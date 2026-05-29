<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['nullable', 'in:admin'],
            'status_akun' => ['nullable', 'in:aktif,nonaktif,kunci'],
        ];
    }

    public function messages(): array
    {
        return [
            'role.in' => 'Dari halaman ini hanya boleh membuat akun admin. Akun pelaku usaha dibuat otomatis dari import/verifikasi PIRT.',
        ];
    }
}
