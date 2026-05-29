<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => ['prohibited'],
            'email' => ['prohibited'],
            'nib' => ['prohibited'],
            'role' => ['prohibited'],
            'password' => ['nullable', 'string', 'min:8'],
            'status_akun' => ['sometimes', 'required', 'in:aktif,nonaktif,kunci'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.prohibited' => 'Nama tidak boleh diubah dari halaman manajemen user.',
            'email.prohibited' => 'Email tidak boleh diubah dari halaman manajemen user.',
            'nib.prohibited' => 'NIB tidak boleh diubah dari halaman manajemen user.',
            'role.prohibited' => 'Role tidak boleh diubah dari halaman manajemen user.',
        ];
    }
}
