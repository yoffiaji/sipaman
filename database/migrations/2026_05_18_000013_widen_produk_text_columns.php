<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('produks') || Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        // File PIRT asli kadang menaruh banyak nama produk/varian dalam satu sel.
        // VARCHAR(500) dipakai agar data tidak terpotong, tetapi tetap aman untuk index.
        DB::statement('ALTER TABLE `produks` MODIFY `nama_branding` VARCHAR(500) NOT NULL');
        DB::statement('ALTER TABLE `produks` MODIFY `nama_toko` VARCHAR(500) NULL');
        DB::statement('ALTER TABLE `produks` MODIFY `jenis_pangan` VARCHAR(500) NULL');
        DB::statement('ALTER TABLE `produks` MODIFY `kategori_pangan` VARCHAR(500) NULL');
        DB::statement('ALTER TABLE `produks` MODIFY `wilayah` VARCHAR(500) NULL');
        DB::statement('ALTER TABLE `produks` MODIFY `no_hp` VARCHAR(100) NULL');
    }

    public function down(): void
    {
        if (! Schema::hasTable('produks') || Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::statement('ALTER TABLE `produks` MODIFY `nama_branding` VARCHAR(150) NOT NULL');
        DB::statement('ALTER TABLE `produks` MODIFY `nama_toko` VARCHAR(150) NULL');
        DB::statement('ALTER TABLE `produks` MODIFY `jenis_pangan` VARCHAR(150) NULL');
        DB::statement('ALTER TABLE `produks` MODIFY `kategori_pangan` VARCHAR(150) NULL');
        DB::statement('ALTER TABLE `produks` MODIFY `wilayah` VARCHAR(150) NULL');
        DB::statement('ALTER TABLE `produks` MODIFY `no_hp` VARCHAR(20) NULL');
    }
};
