<?php

namespace Database\Seeders;

use App\Models\Kecamatan;
use Illuminate\Database\Seeder;

class KecamatanSeeder extends Seeder
{
    public function run(): void
    {
        $kecamatans = [
            'Colomadu',
            'Gondangrejo',
            'Jaten',
            'Jatipuro',
            'Jatiyoso',
            'Jenawi',
            'Jumantono',
            'Jumapolo',
            'Karanganyar',
            'Karangpandan',
            'Kebakkramat',
            'Kerjo',
            'Matesih',
            'Mojogedang',
            'Ngargoyoso',
            'Tasikmadu',
            'Tawangmangu',
        ];

        foreach ($kecamatans as $nama) {
            Kecamatan::updateOrCreate(
                ['nama_kecamatan' => $nama],
                ['kab_kota' => 'Karanganyar']
            );
        }
    }
}
