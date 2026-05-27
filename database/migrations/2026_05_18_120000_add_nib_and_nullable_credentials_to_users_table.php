<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Menyesuaikan tabel `users` dengan flow pelaku usaha:
 *
 * - Tambah kolom `nib` (Nomor Induk Berusaha) sebagai identifier login utama
 *   untuk pelaku usaha. Unique tapi nullable supaya admin/super_admin yang
 *   tidak punya NIB tetap bisa pakai email.
 * - Ubah `password` jadi nullable supaya user auto-create dari import bisa
 *   masuk database tanpa password. Login dengan password null akan ditolak.
 * - Ubah `email` jadi nullable supaya pelaku usaha yang cuma punya NIB
 *   tetap bisa diregistrasi tanpa email palsu.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // NIB sebagai identifier login pelaku usaha
            $table->string('nib', 50)->nullable()->unique()->after('email');
        });

        // SQLite tidak mendukung perubahan kolom langsung pakai change(),
        // jadi gunakan DB statement untuk MySQL. Adjust kalau pakai DB lain.
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            // Email -> nullable (boleh tidak diisi untuk akun yang hanya pakai NIB)
            DB::statement('ALTER TABLE users MODIFY email VARCHAR(150) NULL');
            // Password -> nullable (akun belum aktif sampai admin set password)
            DB::statement('ALTER TABLE users MODIFY password VARCHAR(255) NULL');

            // Drop unique constraint lama pada email (karena MySQL tidak mengizinkan
            // multiple NULL di unique index, sebenarnya MySQL OK dengan multiple NULL
            // pada unique. Tapi kita pastikan dengan rebuild unique index)
            // Catatan: kalau ada error "index doesn't exist", abaikan via try/catch
        } else {
            // Untuk SQLite / driver lain, gunakan Doctrine kalau tersedia
            Schema::table('users', function (Blueprint $table) {
                $table->string('email', 150)->nullable()->change();
                $table->string('password')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['nib']);
            $table->dropColumn('nib');
        });

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            // Hati-hati: rollback akan gagal kalau ada baris dengan email/password NULL.
            // Bersihkan dulu sebelum rollback di production.
            DB::statement('ALTER TABLE users MODIFY email VARCHAR(150) NOT NULL');
            DB::statement('ALTER TABLE users MODIFY password VARCHAR(255) NOT NULL');
        } else {
            Schema::table('users', function (Blueprint $table) {
                $table->string('email', 150)->nullable(false)->change();
                $table->string('password')->nullable(false)->change();
            });
        }
    }
};
