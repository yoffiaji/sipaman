<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pirt_commitment_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->nullable()->constrained('produks')->nullOnDelete();
            $table->string('no_sppirt', 100)->index();
            $table->string('provinsi', 100)->nullable();
            $table->string('kab_kota', 100)->nullable();
            $table->string('nama_pelaku_usaha', 150)->nullable();
            $table->text('alamat_usaha')->nullable();
            $table->string('phone', 30)->nullable();
            $table->date('tanggal_terdaftar')->nullable();
            $table->string('nib', 50)->nullable();
            $table->boolean('verifikasi_produk')->default(false);
            $table->boolean('verifikasi_label')->default(false);
            $table->boolean('pkp')->default(false);
            $table->boolean('cppob_pemeriksaan_sarana')->default(false);
            $table->string('status_pemenuhan_komitmen', 100)->nullable();
            $table->timestamps();

            $table->unique('no_sppirt');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pirt_commitment_statuses');
    }
};
