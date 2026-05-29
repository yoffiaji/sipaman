<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLandingPageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'judul' => ['nullable', 'string', 'max:200'],
            'subjudul' => ['nullable', 'string', 'max:255'],
            'konten' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'image_alt' => ['nullable', 'string', 'max:255'],
            'button_text' => ['nullable', 'string', 'max:100'],
            'button_url' => [
                'nullable',
                'string',
                'max:500',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if ($value === null || $value === '') {
                        return;
                    }

                    if (! preg_match('/^(https?:\/\/|\/|#)/i', (string) $value)) {
                        $fail('Link tombol harus diawali http://, https://, /, atau #.');
                    }
                },
            ],
            'is_active' => ['sometimes', 'boolean'],
            'remove_image' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'judul.max' => 'Judul maksimal 200 karakter.',
            'subjudul.max' => 'Subjudul maksimal 255 karakter.',
            'image.image' => 'Gambar harus berupa file gambar.',
            'image.mimes' => 'Gambar harus berformat JPG, PNG, atau WebP.',
            'image.max' => 'Ukuran gambar maksimal 2 MB.',
            'image_alt.max' => 'Alt gambar maksimal 255 karakter.',
            'button_text.max' => 'Teks tombol maksimal 100 karakter.',
            'button_url.max' => 'Link tombol maksimal 500 karakter.',
            'is_active.boolean' => 'Status aktif harus bernilai aktif atau nonaktif.',
        ];
    }
}
