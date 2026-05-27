<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSystemSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'value' => ['nullable', 'string', 'max:5000'],
            'deskripsi' => ['nullable', 'string', 'max:500'],
        ];
    }
}
