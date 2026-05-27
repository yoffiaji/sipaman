<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migrasi ini memperbarui tabel verifikasi_produks:
 * 1. Tambah kolom pkp dan cppob_pemeriksaan_sarana (sesuai file Excel Status Komitmen)
 * 2. Ubah status_komitmen dari enum menjadi boolean (dihitung otomatis dari 4 kolom)
 * 3. Tambah kolom catatan untuk keperluan manual verifikasi
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('verifikasi_produks', function (Blueprint $table) {
            // Tambah kolom PKP dan CPPOB sesuai file Excel Status Komitmen
            $table->boolean('pkp')->default(false)->after('verifikasi_label');
            $table->boolean('cppob_pemeriksaan_sarana')->default(false)->after('pkp');

            // Ganti enum status_komitmen dengan boolean (dihitung otomatis)
            $table->dropColumn('status_komitmen');
        });

        Schema::table('verifikasi_produks', function (Blueprint $table) {
            $table->boolean('status_komitmen')->default(false)->after('cppob_pemeriksaan_sarana');
            $table->text('catatan')->nullable()->after('status_komitmen');
        });
    }

    public function down(): void
    {
        Schema::table('verifikasi_produks', function (Blueprint $table) {
            $table->dropColumn(['pkp', 'cppob_pemeriksaan_sarana', 'status_komitmen', 'catatan']);
        });

        Schema::table('verifikasi_produks', function (Blueprint $table) {
            $table->enum('status_komitmen', ['ya', 'tidak'])->default('tidak');
        });
    }
};
