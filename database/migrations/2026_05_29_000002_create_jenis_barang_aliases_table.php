<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jenis_barang_aliases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_barang_id')->constrained('jenis_barangs')->cascadeOnDelete();
            $table->string('keyword', 160)->unique();
            $table->unsignedSmallInteger('priority')->default(100);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jenis_barang_aliases');
    }
};
