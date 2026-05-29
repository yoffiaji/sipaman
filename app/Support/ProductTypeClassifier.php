<?php

namespace App\Support;

use App\Models\JenisBarang;
use App\Models\JenisBarangAlias;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ProductTypeClassifier
{
    private const DEFAULT_DESCRIPTIONS = [
        'Makanan Ringan' => 'Camilan kering, keripik, kerupuk, rengginang, dan snack sejenis.',
        'Roti & Kue' => 'Produk bakery, roti, cake, cookies, biskuit, dan jajanan kue.',
        'Minuman' => 'Minuman siap saji, minuman serbuk, teh, kopi, sari buah, dan botanikal.',
        'Bumbu & Sambal' => 'Bumbu olahan, sambal, saus, rempah, dan penyedap pangan.',
        'Olahan Hewani' => 'Produk olahan ikan, ayam, daging, telur, abon, dan pangan hewani lain.',
        'Olahan Buah & Sayur' => 'Produk berbasis buah, sayur, selai, manisan, asinan, dan olahan nabati basah/kering.',
        'Olahan Kacang, Biji & Umbi' => 'Produk berbahan kacang, biji-bijian, serealia, singkong, ubi, kentang, dan tepung.',
        'Gula, Madu & Pemanis' => 'Produk gula, madu, sirup, permen, cokelat, dan pemanis sejenis.',
        'Makanan Siap Saji' => 'Produk pangan olahan siap konsumsi atau siap dipanaskan.',
        'Lainnya / Perlu Review' => 'Data belum cocok dengan aturan klasifikasi dan perlu direview admin.',
    ];

    private const KEYWORD_RULES = [
        'Makanan Ringan' => [
            'makanan ringan', 'snack', 'keripik', 'kripik', 'kerupuk', 'rambak', 'rengginang', 'rempeyek', 'peyek', 'stik', 'stick', 'opak', 'emping', 'makaroni', 'basreng', 'camilan', 'cemilan',
        ],
        'Roti & Kue' => [
            'bakery', 'roti', 'kue', 'cake', 'kukis', 'cookies', 'biskuit', 'nastar', 'bolu', 'brownies', 'donat', 'pastry', 'pie', 'bakpia',
        ],
        'Minuman' => [
            'minuman', 'teh', 'kopi', 'sari buah', 'sirup minuman', 'jamu', 'wedang', 'botanikal', 'serbuk minuman', 'minuman serbuk', 'susu', 'coklat bubuk',
        ],
        'Bumbu & Sambal' => [
            'bumbu', 'sambal', 'saus', 'saos', 'rempah', 'penyedap', 'kaldu', 'abon cabe', 'serundeng', 'bawang goreng',
        ],
        'Olahan Hewani' => [
            'ikan', 'daging', 'ayam', 'sapi', 'telur', 'abon', 'usus', 'paru', 'bakso', 'nugget', 'sosis', 'udang', 'teri', 'lele', 'bandeng',
        ],
        'Olahan Buah & Sayur' => [
            'buah', 'sayur', 'selai', 'manisan', 'asinan', 'pisang', 'salak', 'nangka', 'apel', 'mangga', 'jamur', 'kentang sayur', 'tomat',
        ],
        'Olahan Kacang, Biji & Umbi' => [
            'kacang', 'biji', 'umbi', 'singkong', 'ubi', 'talas', 'kentang', 'tepung', 'beras', 'jagung', 'kedelai', 'tempe', 'sereal', 'serealia', 'hasil olahan biji',
        ],
        'Gula, Madu & Pemanis' => [
            'gula', 'madu', 'permen', 'cokelat', 'coklat', 'sirup', 'karamel', 'jelly', 'agar', 'pemanis',
        ],
        'Makanan Siap Saji' => [
            'siap saji', 'rendang', 'gudeg', 'lauk', 'nasi', 'mie', 'mi ', 'mi-', 'frozen food', 'beku', 'instan',
        ],
    ];

    public function resolve(?string $kategoriPangan, ?string $jenisPangan): ?JenisBarang
    {
        $haystack = $this->normalize(trim(($kategoriPangan ?? '').' '.($jenisPangan ?? '')));

        if ($haystack === '') {
            return null;
        }

        $fromAlias = $this->resolveFromDatabaseAliases($haystack);

        if ($fromAlias) {
            return $fromAlias;
        }

        foreach (self::KEYWORD_RULES as $category => $keywords) {
            foreach ($keywords as $keyword) {
                if (str_contains($haystack, $this->normalize($keyword))) {
                    return $this->firstOrCreateCategory($category);
                }
            }
        }

        return $this->firstOrCreateCategory('Lainnya / Perlu Review');
    }

    public function seedDefaults(): void
    {
        foreach (array_keys(self::DEFAULT_DESCRIPTIONS) as $category) {
            $jenisBarang = $this->firstOrCreateCategory($category);

            if (! Schema::hasTable('jenis_barang_aliases')) {
                continue;
            }

            foreach (self::KEYWORD_RULES[$category] ?? [] as $priority => $keyword) {
                JenisBarangAlias::updateOrCreate(
                    ['keyword' => $this->normalize($keyword)],
                    [
                        'jenis_barang_id' => $jenisBarang->id,
                        'priority' => $priority + 1,
                        'is_active' => true,
                    ]
                );
            }
        }
    }

    private function resolveFromDatabaseAliases(string $haystack): ?JenisBarang
    {
        if (! Schema::hasTable('jenis_barang_aliases')) {
            return null;
        }

        $aliases = JenisBarangAlias::query()
            ->with('jenisBarang')
            ->where('is_active', true)
            ->orderBy('priority')
            ->get();

        foreach ($aliases as $alias) {
            if ($alias->keyword && str_contains($haystack, $this->normalize($alias->keyword))) {
                return $alias->jenisBarang;
            }
        }

        return null;
    }

    private function firstOrCreateCategory(string $name): JenisBarang
    {
        $data = ['nama_jenis' => $name];
        $slug = Str::slug($name);

        if (Schema::hasColumn('jenis_barangs', 'slug')) {
            $data['slug'] = $slug;
        }

        if (Schema::hasColumn('jenis_barangs', 'deskripsi')) {
            $data['deskripsi'] = self::DEFAULT_DESCRIPTIONS[$name] ?? null;
        }

        if (Schema::hasColumn('jenis_barangs', 'is_active')) {
            $data['is_active'] = true;
        }

        $existing = JenisBarang::query()
            ->when(
                Schema::hasColumn('jenis_barangs', 'slug'),
                fn ($query) => $query->where('slug', $slug)->orWhere('nama_jenis', $name),
                fn ($query) => $query->where('nama_jenis', $name)
            )
            ->first();

        if ($existing) {
            $dirty = array_filter(
                $data,
                fn ($value, $key) => $value !== null && blank($existing->{$key}),
                ARRAY_FILTER_USE_BOTH
            );

            if ($dirty !== []) {
                $existing->fill($dirty)->save();
            }

            return $existing;
        }

        return JenisBarang::create($data);
    }

    private function normalize(string $value): string
    {
        $value = Str::lower(Str::ascii($value));
        $value = preg_replace('/[^a-z0-9]+/', ' ', $value) ?: $value;
        $value = preg_replace('/\s+/', ' ', $value) ?: $value;

        return trim($value);
    }
}
