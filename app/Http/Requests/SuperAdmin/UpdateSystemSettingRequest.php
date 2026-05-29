<?php

namespace App\Http\Requests\SuperAdmin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

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

    public function messages(): array
    {
        return [
            'value.max' => 'Nilai pengaturan maksimal 5000 karakter.',
            'deskripsi.max' => 'Deskripsi pengaturan maksimal 500 karakter.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $setting = $this->route('setting');
            $key = strtolower((string) ($setting?->key ?? ''));

            if (preg_match('/(password|secret|token|api_key|private_key)/', $key)) {
                $validator->errors()->add(
                    'value',
                    'System Settings tidak boleh dipakai untuk menyimpan password, token, API key, atau secret.'
                );
            }
        });
    }
}
