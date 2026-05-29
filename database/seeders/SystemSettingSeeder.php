<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use App\Support\SystemSettings;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'site_name' => [
                'value' => 'SIPAMAN',
                'deskripsi' => 'Nama aplikasi yang tampil di layout publik, auth, dan admin.',
            ],
            'site_tagline' => [
                'value' => 'Sistem Informasi Pangan Aman',
                'deskripsi' => 'Tagline global aplikasi.',
            ],
            'footer_text' => [
                'value' => 'SIPAMAN - Sistem Informasi Pangan Aman',
                'deskripsi' => 'Teks singkat pada footer publik.',
            ],
            'contact_email' => [
                'value' => 'dinkes@karanganyarkab.go.id',
                'deskripsi' => 'Email kontak publik.',
            ],
            'contact_whatsapp' => [
                'value' => '',
                'deskripsi' => 'Nomor WhatsApp kontak publik, isi tanpa menyimpan data rahasia.',
            ],
            'office_address' => [
                'value' => 'Jl. Lawu No. 385, Karanganyar, Jawa Tengah 57711',
                'deskripsi' => 'Alamat kantor yang tampil pada footer publik.',
            ],
            'office_hours' => [
                'value' => 'Senin - Jumat, 08.00 - 16.00 WIB',
                'deskripsi' => 'Jam layanan publik.',
            ],
            'logo_path' => [
                'value' => '',
                'deskripsi' => 'Path logo pada storage public jika sudah tersedia.',
            ],
            'default_pagination' => [
                'value' => '10',
                'deskripsi' => 'Jumlah data default per halaman jika dipakai fitur pagination.',
            ],
            'import_max_file_size_kb' => [
                'value' => '10240',
                'deskripsi' => 'Batas maksimal upload file import dalam KB.',
            ],
        ];

        foreach ($settings as $key => $data) {
            $setting = SystemSetting::firstOrNew(['key' => $key]);

            if (! $setting->exists) {
                $setting->value = $data['value'];
            }

            $setting->deskripsi = $data['deskripsi'];
            $setting->save();
        }

        SystemSettings::forget();
    }
}
