<?php

namespace Database\Seeders;

use App\Models\LandingPageContent;
use Illuminate\Database\Seeder;

class LandingPageContentSeeder extends Seeder
{
    public function run(): void
    {
        $sections = [
            'hero' => [
                'judul' => 'SIPAMAN',
                'subjudul' => 'Sistem Informasi Pangan Aman',
                'konten' => 'Temukan produk PIRT, pelaku usaha, dan potensi UMKM pangan aman dari Karanganyar dalam satu katalog yang mudah dicari.',
                'image_alt' => 'Produk pangan aman Karanganyar',
                'button_text' => 'Lihat Produk',
                'button_url' => '/products',
            ],
            'featured_products' => [
                'judul' => 'Produk Pangan Terverifikasi',
                'subjudul' => 'Direktori',
                'konten' => 'Produk lokal Karanganyar yang sudah terverifikasi dan siap dikenalkan ke publik.',
                'image_alt' => 'Produk PIRT terverifikasi',
                'button_text' => 'Lihat Semua Produk',
                'button_url' => '/products',
            ],
            'region_potential' => [
                'judul' => 'Potensi Lokal Tiap Kecamatan',
                'subjudul' => 'Sebaran Wilayah',
                'konten' => 'SIPAMAN membantu masyarakat melihat produk PIRT, pelaku usaha, dan persebaran potensi pangan aman dari wilayah Karanganyar.',
                'image_alt' => 'Potensi pangan aman Karanganyar',
                'button_text' => 'Jelajahi UMKM',
                'button_url' => '/umkm',
            ],
        ];

        foreach ($sections as $sectionKey => $data) {
            $content = LandingPageContent::firstOrNew(['section_key' => $sectionKey]);

            foreach ($data as $field => $value) {
                if (! $content->exists || $content->{$field} === null || $content->{$field} === '') {
                    $content->{$field} = $value;
                }
            }

            if (! $content->exists) {
                $content->is_active = true;
            }

            $content->save();
        }
    }
}
