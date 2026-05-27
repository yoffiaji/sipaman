<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama' => ['sometimes', 'required', 'string', 'max:150'],
            'email' => ['sometimes', 'required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($this->route('user')?->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'role' => ['sometimes', 'required', 'in:user,admin'],
            'status_akun' => ['sometimes', 'required', 'in:aktif,nonaktif,kunci'],
        ];
    }
}
