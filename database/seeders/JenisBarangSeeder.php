<?php

namespace Database\Seeders;

use App\Support\ProductTypeClassifier;
use Illuminate\Database\Seeder;

class JenisBarangSeeder extends Seeder
{
    public function run(): void
    {
        app(ProductTypeClassifier::class)->seedDefaults();
    }
}
