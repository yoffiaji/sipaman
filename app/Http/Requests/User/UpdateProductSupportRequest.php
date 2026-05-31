<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductSupportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $protected = [
            'no_sppirt',
            'nib',
            'nama_branding',
            'nama_pelaku_usaha',
            'nama_toko',
            'alamat',
            'alamat_toko',
            'kategori_pangan',
            'jenis_pangan',
            'kemasan',
            'cara_penyimpanan',
            'wilayah',
            'kecamatan_id',
            'jenis_barang_id',
            'is_verified',
            'tanggal_pengajuan',
            'tanggal_verifikasi',
            'masa_berlaku_pirt',
            'status_oss',
            'no_hp',
            'user_id',
        ];

        return array_merge(
            collect($protected)->mapWithKeys(fn (string $field) => [$field => ['prohibited']])->all(),
            [
                'harga' => ['sometimes', 'nullable', 'integer', 'min:0', 'max:1000000000'],
                'deskripsi' => ['sometimes', 'nullable', 'string', 'max:2000'],
            ]
        );
    }

    public function messages(): array
    {
        return [
            '*.prohibited' => 'Pelaku usaha hanya boleh mengubah harga, deskripsi tampilan, dan gambar produk.',
            'harga.integer' => 'Harga harus berupa angka bulat.',
            'harga.min' => 'Harga tidak boleh negatif.',
            'harga.max' => 'Harga terlalu besar.',
            'deskripsi.max' => 'Deskripsi maksimal 2000 karakter.',
        ];
    }
}
