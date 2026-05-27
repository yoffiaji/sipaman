<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * PERBAIKAN AUDIT #3: Tambah index pada kolom yang sering difilter
 * ----------------------------------------------------------------
 * produks.is_verified      → sering difilter di scopeVerified()
 * produks.kecamatan_id     → sering difilter di scopeByKecamatan()
 * produks.jenis_barang_id  → sering difilter di scopeByJenisBarang()
 * produks.nama_branding    → sering di-search
 * audit_trails             → sering diquery by user + tanggal
 * activity_logs            → sering diquery by user + tanggal
 * gambar_produks           → sering diquery by produk + is_primary
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produks', function (Blueprint $table) {
            $table->index('is_verified', 'idx_produks_is_verified');
            $table->index('user_id', 'idx_produks_user');
            $table->index('kecamatan_id', 'idx_produks_kecamatan');
            $table->index('jenis_barang_id', 'idx_produks_jenis_barang');
            $table->index('nama_branding', 'idx_produks_nama_branding');
            $table->index('masa_berlaku_pirt', 'idx_produks_masa_berlaku_pirt');
        });

        Schema::table('audit_trails', function (Blueprint $table) {
            $table->index(['user_id', 'created_at'], 'idx_audit_user_created');
            $table->index('aksi', 'idx_audit_aksi');
            $table->index('tabel_terkait', 'idx_audit_tabel');
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->index(['user_id', 'created_at'], 'idx_activity_user_created');
        });

        Schema::table('gambar_produks', function (Blueprint $table) {
            $table->index(['produk_id', 'is_primary'], 'idx_gambar_produk_primary');
        });
    }

    public function down(): void
    {
        Schema::table('produks', function (Blueprint $table) {
            $table->dropIndex('idx_produks_is_verified');
            $table->dropIndex('idx_produks_user');
            $table->dropIndex('idx_produks_kecamatan');
            $table->dropIndex('idx_produks_jenis_barang');
            $table->dropIndex('idx_produks_nama_branding');
            $table->dropIndex('idx_produks_masa_berlaku_pirt');
        });

        Schema::table('audit_trails', function (Blueprint $table) {
            $table->dropIndex('idx_audit_user_created');
            $table->dropIndex('idx_audit_aksi');
            $table->dropIndex('idx_audit_tabel');
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex('idx_activity_user_created');
        });

        Schema::table('gambar_produks', function (Blueprint $table) {
            $table->dropIndex('idx_gambar_produk_primary');
        });
    }
};
