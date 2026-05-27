<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateJenisBarangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_jenis' => ['required', 'string', 'max:150', Rule::unique('jenis_barangs', 'nama_jenis')->ignore($this->route('jenisBarang')?->id)],
        ];
    }
}
