<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jenis_barangs', function (Blueprint $table) {
            if (! Schema::hasColumn('jenis_barangs', 'slug')) {
                $table->string('slug', 160)->nullable()->unique()->after('nama_jenis');
            }

            if (! Schema::hasColumn('jenis_barangs', 'deskripsi')) {
                $table->text('deskripsi')->nullable()->after('slug');
            }

            if (! Schema::hasColumn('jenis_barangs', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('deskripsi');
            }
        });
    }

    public function down(): void
    {
        Schema::table('jenis_barangs', function (Blueprint $table) {
            if (Schema::hasColumn('jenis_barangs', 'is_active')) {
                $table->dropColumn('is_active');
            }

            if (Schema::hasColumn('jenis_barangs', 'deskripsi')) {
                $table->dropColumn('deskripsi');
            }

            if (Schema::hasColumn('jenis_barangs', 'slug')) {
                $table->dropUnique(['slug']);
                $table->dropColumn('slug');
            }
        });
    }
};
