<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductVerificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'verifikasi_produk' => ['required', 'boolean'],
            'verifikasi_label' => ['required', 'boolean'],
            'pkp' => ['required', 'boolean'],
            'cppob_pemeriksaan_sarana' => ['required', 'boolean'],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
