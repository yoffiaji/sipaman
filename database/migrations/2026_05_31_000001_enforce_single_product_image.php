<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    public function up(): void
    {
        $duplicateProdukIds = DB::table('gambar_produks')
            ->select('produk_id')
            ->groupBy('produk_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('produk_id');

        foreach ($duplicateProdukIds as $produkId) {
            $images = DB::table('gambar_produks')
                ->where('produk_id', $produkId)
                ->orderByDesc('is_primary')
                ->orderByDesc('uploaded_at')
                ->orderByDesc('id')
                ->get();

            $keep = $images->first();
            $remove = $images->skip(1);

            if (! $keep) {
                continue;
            }

            DB::table('gambar_produks')
                ->where('id', $keep->id)
                ->update(['is_primary' => true]);

            $removeIds = $remove->pluck('id')->all();

            if ($removeIds !== []) {
                DB::table('gambar_produks')->whereIn('id', $removeIds)->delete();

                foreach ($remove->pluck('url_gambar')->filter()->unique() as $path) {
                    Storage::disk('public')->delete($path);
                }
            }
        }

        DB::table('gambar_produks')->update(['is_primary' => true]);

        Schema::table('gambar_produks', function (Blueprint $table) {
            $table->unique('produk_id', 'gambar_produks_produk_id_unique');
        });
    }

    public function down(): void
    {
        Schema::table('gambar_produks', function (Blueprint $table) {
            $table->dropUnique('gambar_produks_produk_id_unique');
        });
    }
};
