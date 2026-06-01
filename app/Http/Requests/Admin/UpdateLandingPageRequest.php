<?php

namespace App\Http\Requests\Admin;

use App\Models\LandingPageContent;
use App\Services\LandingPageContentService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLandingPageRequest extends FormRequest
{
    private const BUTTON_URL_CHOICES = [
        'products' => '/products',
        'umkm' => '/umkm',
        'home' => '/',
        'none' => null,
    ];

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'button_url' => $this->resolveButtonUrl('button_url_type', 'custom_button_url'),
            'secondary_button_url' => $this->resolveButtonUrl('secondary_button_url_type', 'secondary_custom_button_url'),
        ]);
    }

    public function rules(): array
    {
        $allowsImage = $this->sectionKey() === 'hero';
        $allowsSecondaryButton = $this->sectionKey() === 'hero';

        return [
            'judul' => ['nullable', 'string', 'max:200'],
            'subjudul' => ['nullable', 'string', 'max:255'],
            'konten' => ['nullable', 'string'],
            'image' => ['nullable', Rule::prohibitedIf(! $allowsImage), 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'image_alt' => ['nullable', Rule::prohibitedIf(! $allowsImage), 'string', 'max:255'],
            'button_text' => ['nullable', 'string', 'max:100'],
            'button_url_type' => ['nullable', Rule::in(['products', 'umkm', 'home', 'none', 'custom'])],
            'custom_button_url' => ['nullable', 'string', 'max:500'],
            'button_url' => $this->buttonUrlRules(),
            'secondary_button_text' => ['nullable', Rule::prohibitedIf(! $allowsSecondaryButton), 'string', 'max:100'],
            'secondary_button_url_type' => ['nullable', Rule::prohibitedIf(! $allowsSecondaryButton), Rule::in(['products', 'umkm', 'home', 'none', 'custom'])],
            'secondary_custom_button_url' => ['nullable', Rule::prohibitedIf(! $allowsSecondaryButton), 'string', 'max:500'],
            'secondary_button_url' => [
                Rule::prohibitedIf(! $allowsSecondaryButton),
                ...$this->buttonUrlRules(),
            ],
            'is_active' => ['sometimes', 'boolean'],
            'remove_image' => ['nullable', Rule::prohibitedIf(! $allowsImage), 'boolean'],
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
            'image.prohibited' => 'Gambar hanya dapat diubah pada Banner Utama.',
            'image_alt.max' => 'Alt gambar maksimal 255 karakter.',
            'image_alt.prohibited' => 'Keterangan gambar hanya dapat diubah pada Banner Utama.',
            'button_text.max' => 'Teks tombol maksimal 100 karakter.',
            'button_url.max' => 'Tujuan tombol maksimal 500 karakter.',
            'button_url_type.in' => 'Pilihan tujuan tombol tidak valid.',
            'custom_button_url.max' => 'Link khusus maksimal 500 karakter.',
            'secondary_button_text.max' => 'Teks tombol kedua maksimal 100 karakter.',
            'secondary_button_text.prohibited' => 'Tombol kedua hanya tersedia pada Banner Utama.',
            'secondary_button_url.max' => 'Tujuan tombol kedua maksimal 500 karakter.',
            'secondary_button_url.prohibited' => 'Tombol kedua hanya tersedia pada Banner Utama.',
            'secondary_button_url_type.in' => 'Pilihan tujuan tombol kedua tidak valid.',
            'secondary_button_url_type.prohibited' => 'Tombol kedua hanya tersedia pada Banner Utama.',
            'secondary_custom_button_url.max' => 'Link khusus tombol kedua maksimal 500 karakter.',
            'secondary_custom_button_url.prohibited' => 'Tombol kedua hanya tersedia pada Banner Utama.',
            'is_active.boolean' => 'Status aktif harus bernilai aktif atau nonaktif.',
            'remove_image.prohibited' => 'Gambar hanya dapat dihapus dari Banner Utama.',
        ];
    }

    public function contentData(): array
    {
        $data = $this->validated();

        unset($data['button_url_type'], $data['custom_button_url'], $data['secondary_button_url_type'], $data['secondary_custom_button_url']);

        if ($this->input('button_url_type') === 'none') {
            $data['button_text'] = null;
            $data['button_url'] = null;
        }

        if ($this->input('secondary_button_url_type') === 'none') {
            $data['secondary_button_text'] = null;
            $data['secondary_button_url'] = null;
        }

        if ($this->sectionKey() !== 'hero') {
            unset($data['image'], $data['remove_image'], $data['image_alt'], $data['secondary_button_text'], $data['secondary_button_url']);
        }

        return $data;
    }

    private function sectionKey(): ?string
    {
        $landingPage = $this->route('landingPage');

        if ($landingPage instanceof LandingPageContent) {
            return $landingPage->section_key;
        }

        $section = $this->route('section');

        if (is_string($section) && array_key_exists($section, LandingPageContentService::MANAGED_SECTIONS)) {
            return $section;
        }

        return null;
    }

    private function resolveButtonUrl(string $typeField, string $customField): ?string
    {
        $type = $this->input($typeField, 'custom');

        if (array_key_exists($type, self::BUTTON_URL_CHOICES)) {
            return self::BUTTON_URL_CHOICES[$type];
        }

        return $type === 'custom' ? $this->input($customField) : null;
    }

    private function buttonUrlRules(): array
    {
        return [
            'nullable',
            'string',
            'max:500',
            function (string $attribute, mixed $value, \Closure $fail): void {
                if ($value === null || $value === '') {
                    return;
                }

                if (! preg_match('/^(https?:\/\/|\/|#)/i', (string) $value)) {
                    $fail('Tujuan tombol harus diawali http://, https://, /, atau #.');
                }
            },
        ];
    }
}
